<?php

declare(strict_types=1);

namespace Drupal\niklan\Loader\ExternalContent;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Site\Settings;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;
use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Data\IdentifiedSource;
use Drupal\external_content\Data\IdentifiedSourceBundle;
use Drupal\external_content\Data\LoaderResult;
use Drupal\external_content\Node\Element;
use Drupal\media\MediaInterface;
use Drupal\niklan\Asset\ContentAssetManager;
use Drupal\niklan\Entity\Node\BlogEntryInterface;
use Drupal\niklan\Exception\InvalidContentSource;
use Drupal\niklan\Helper\PathHelper;
use Drupal\niklan\Node\ExternalContent\DrupalMedia;
use Drupal\niklan\Node\ExternalContent\RemoteVideo;
use Drupal\niklan\Node\ExternalContent\Video;
use Drupal\taxonomy\TermStorageInterface;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class Blog implements LoaderInterface, EnvironmentAwareInterface {

  /**
   * {@selfdoc}
   */
  private EnvironmentInterface $environment;

  /**
   * {@selfdoc}
   */
  public function __construct(
    private ExternalContentManagerInterface $externalContentManager,
    private EntityTypeManagerInterface $entityTypeManager,
    private ContentAssetManager $contentAssetManager,
    private LoggerChannelInterface $logger,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function load(IdentifiedSourceBundle $bundle): LoaderResult {
    $this->logger->info('Starting to load blog external content bundle with ID: ' . $bundle->id);
    $blog_entry = $this->findBlogEntry($bundle->id);

    foreach ($bundle->getAllWithAttribute('language')->sources() as $identified_source) {
      // Switch the content language to be the same as variation.
      $language = $identified_source->attributes->getAttribute('language');
      $blog_entry = $blog_entry->getTranslation($language);
      $this->syncBlogEntryVariation($blog_entry, $identified_source);
    }

    // @todo Add some checks to avoid unnecessary saving.
    $blog_entry->save();
    $this->logger->info(\sprintf(
      'External content bundle %s synced with node:%s',
      $bundle->id,
      $blog_entry->id(),
    ));

    return LoaderResult::withResults($bundle->id, [
      'entity_type_id' => $blog_entry->getEntityTypeId(),
      'entity_id' => $blog_entry->id(),
    ]);
  }

  /**
   * {@selfdoc}
   */
  private function findBlogEntry(string $external_id): ?BlogEntryInterface {
    $storage = $this->entityTypeManager->getStorage('node');

    $ids = $storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'blog_entry')
      ->condition('external_id', $external_id)
      ->range(0, 1)
      ->execute();

    if (!$ids) {
      $this->logger->info(\sprintf(
        'External content for ID %s not found, creating a new entity.',
        $external_id,
      ));
      // Create a new one if nothing is found.
      $blog_entry = $storage->create(['type' => 'blog_entry']);
      \assert($blog_entry instanceof BlogEntryInterface);
      $blog_entry->setExternalId($external_id);

      return $blog_entry;
    }

    /* @phpstan-ignore-next-line */
    return $storage->load(\reset($ids));
  }

  /**
   * {@selfdoc}
   */
  private function syncBlogEntryVariation(BlogEntryInterface $blog_entry, IdentifiedSource $identified_source): void {
    $this->validateSource($identified_source);

    $this->syncTitle($blog_entry, $identified_source);
    $this->syncDates($blog_entry, $identified_source);
    $this->syncDescription($blog_entry, $identified_source);
    $this->syncTags($blog_entry, $identified_source);
    $this->syncPromoImage($blog_entry, $identified_source);
    $this->syncAttachments($blog_entry, $identified_source);
    $this->syncExternalContent($blog_entry, $identified_source);
  }

  /**
   * {@selfdoc}
   */
  private function validateSource(IdentifiedSource $identified_source): void {
    $front_matter = $identified_source->source->data()->get('front_matter');
    $required_front_matter = [
      'id',
      'language',
      'title',
      'created',
      'updated',
      'description',
      'promo',
    ];

    $diff = \array_diff($required_front_matter, \array_keys($front_matter));

    if ($diff) {
      $message = \sprintf(
        "The source %s doesn't have required Front Matter values: %s",
        $identified_source->id,
        \implode(', ', $diff),
      );

      throw new InvalidContentSource($message);
    }
  }

  /**
   * {@selfdoc}
   */
  private function syncTitle(BlogEntryInterface $blog_entry, IdentifiedSource $identified_source): void {
    $front_matter = $identified_source->source->data()->get('front_matter');
    $blog_entry->setTitle($front_matter['title']);
  }

  /**
   * {@selfdoc}
   */
  private function syncDates(BlogEntryInterface $blog_entry, IdentifiedSource $identified_source): void {
    $front_matter = $identified_source->source->data()->get('front_matter');

    $created = DrupalDateTime::createFromFormat(
      format: DateTimeItemInterface::DATETIME_STORAGE_FORMAT,
      time: $front_matter['created'],
    );
    $blog_entry->setCreatedTime($created->getTimestamp());

    $updated = DrupalDateTime::createFromFormat(
      format: DateTimeItemInterface::DATETIME_STORAGE_FORMAT,
      time: $front_matter['updated'],
    );
    $blog_entry->setChangedTime($updated->getTimestamp());
  }

  /**
   * {@selfdoc}
   */
  private function syncDescription(BlogEntryInterface $blog_entry, IdentifiedSource $identified_source): void {
    $front_matter = $identified_source->source->data()->get('front_matter');
    $blog_entry->set('body', ['value' => $front_matter['description']]);
  }

  /**
   * {@selfdoc}
   */
  private function syncTags(BlogEntryInterface $blog_entry, IdentifiedSource $identified_source): void {
    $front_matter = $identified_source->source->data()->get('front_matter');
    $blog_entry->set('field_tags', NULL);

    if (!\array_key_exists('tags', $front_matter) || !\is_array($front_matter['tags'])) {
      return;
    }

    $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
    \assert($term_storage instanceof TermStorageInterface);
    $tag_ids = $term_storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('vid', 'tags')
      ->condition('langcode', $blog_entry->language()->getId())
      // New tags are not created during sync.
      ->condition('name', $front_matter['tags'], 'IN')
      ->sort('name')
      ->execute();

    $field_tags_value = \array_map(
      callback: static fn (string $tag_id) => ['target_id' => $tag_id],
      array: $tag_ids,
    );
    $blog_entry->set('field_tags', \array_values($field_tags_value));
  }

  /**
   * {@selfdoc}
   */
  private function syncPromoImage(BlogEntryInterface $blog_entry, IdentifiedSource $identified_source): void {
    $front_matter = $identified_source->source->data()->get('front_matter');
    $blog_entry->set('field_media_image', NULL);

    if (!isset($front_matter['promo'])) {
      return;
    }

    $promo_uri = $front_matter['promo'];
    $promo_pathname = "{$this->getSourceDir($identified_source)}/$promo_uri";

    if (!\file_exists($promo_pathname)) {
      return;
    }

    $media = $this->contentAssetManager->syncWithMedia($promo_pathname);

    if (!$media instanceof MediaInterface) {
      return;
    }

    $blog_entry->set('field_media_image', ['target_id' => $media->id()]);
  }

  /**
   * {@selfdoc}
   */
  private function syncAttachments(BlogEntryInterface $blog_entry, IdentifiedSource $identified_source): void {
    $front_matter = $identified_source->source->data()->get('front_matter');
    $blog_entry->set('field_media_attachments', NULL);

    if (!isset($front_matter['attachments'])) {
      return;
    }

    foreach ($front_matter['attachments'] as $attachment) {
      $attachment_pathname = "{$this->getSourceDir($identified_source)}/{$attachment['path']}";
      $media = $this->contentAssetManager->syncWithMedia($attachment_pathname);

      if (!$media instanceof MediaInterface) {
        return;
      }

      $blog_entry
        ->get('field_media_attachments')
        ->appendItem(['target_id' => $media->id()]);
    }
  }

  /**
   * {@selfdoc}
   */
  private function syncExternalContent(BlogEntryInterface $blog_entry, IdentifiedSource $identified_source): void {
    $html = $this->externalContentManager->getConverterManager()->convert(
      input: $identified_source->source,
      environment: $this->environment,
    );
    $content = $this->externalContentManager->getHtmlParserManager()->parse(
      html: $html,
      environment: $this->environment,
    );

    $source_dir = $this->getSourceDir($identified_source);

    $this->replaceMediaImages($content, $source_dir);
    $this->replaceMediaVideos($content, $source_dir);
    $this->replaceMediaRemoteVideos($content);
    $this->prepareLinks($content, $source_dir);
    $normalized = $this
      ->externalContentManager
      ->getSerializerManager()
      ->normalize($content, $this->environment);

    $additional_info = [
      // For internal links. MD5 is used instead clear value for a smaller size
      // of the stored data.
      'pathname_md5' => \md5($identified_source->source->data()->get('pathname')),
    ];

    $blog_entry->set('external_content', [
      'value' => $normalized,
      'environment_id' => $this->environment->id(),
      'data' => \json_encode($additional_info),
    ]);
  }

  /**
   * {@selfdoc}
   */
  private function getSourceDir(IdentifiedSource $identified_source): string {
    return \dirname($identified_source->source->data()->get('pathname'));
  }

  /**
   * {@selfdoc}
   */
  private function replaceMediaRemoteVideos(NodeInterface $node): void {
    foreach ($node->getChildren() as $child) {
      \assert($node instanceof NodeInterface);
      $this->replaceMediaRemoteVideos($child);
    }

    if (!$node instanceof RemoteVideo) {
      return;
    }

    $media = $this->contentAssetManager->syncWithMedia($node->src);

    if (!$media instanceof MediaInterface) {
      return;
    }

    $new_node = new DrupalMedia(
      type: 'remote_video',
      uuid: $media->uuid(),
    );
    $node->getParent()->replaceNode($node, $new_node);
  }

  /**
   * {@selfdoc}
   */
  private function replaceMediaImages(NodeInterface $node, string $source_dir): void {
    foreach ($node->getChildren() as $child) {
      \assert($node instanceof NodeInterface);
      $this->replaceMediaImages($child, $source_dir);
    }

    if (!$node instanceof Element || $node->getTag() !== 'img') {
      return;
    }

    $src = $node->getAttributes()->getAttribute('src');

    if (!UrlHelper::isExternal($src)) {
      $src = "$source_dir/$src";
    }

    $media = $this->contentAssetManager->syncWithMedia($src);

    if (!$media instanceof MediaInterface) {
      return;
    }

    $new_node = new DrupalMedia(
      type: 'image',
      uuid: $media->uuid(),
      data: new Data([
        'alt' => $node->getAttributes()->getAttribute('alt'),
        'title' => $node->getAttributes()->getAttribute('title'),
      ]),
    );

    $replace_target = $this->findMediaImageReplaceTarget($node);
    $replace_target->getParent()->replaceNode($replace_target, $new_node);
  }

  /**
   * Find target replacement for media image node.
   *
   * If the image is the only element in the paragraph, it will become a
   * 'lightbox' image, which will be rendered as a separate element with its own
   * container. Because a '<p>' element cannot contain block-level elements,
   * this element will be directly replaced by the media image element.
   *
   * Without that, the result will be rendered from:
   * @code
   *  <p><img src="#"/></p>
   * @endcode
   *
   * to:
   * @code
   *  <p></p>
   *  <div><img src="#"/></div>
   *  <p></p>
   * @endcode.
   */
  public function findMediaImageReplaceTarget(Element $node): NodeInterface {
    $replace_target = $node;
    $parent = $node->getParent();

    // If the parent contains more than one child, this means that we are
    // processing an inline image. Currently, this is not supported, but if it
    // is detected, the surrounding contents will not be removed.
    if ($parent instanceof Element && $parent->getTag() === 'p' && $parent->getChildren()->count() === 1) {
      $replace_target = $parent;
    }

    return $replace_target;
  }

  /**
   * {@selfdoc}
   */
  private function prepareLinks(NodeInterface $node, string $source_dir): void {
    foreach ($node->getChildren() as $child) {
      $this->prepareLinks($child, $source_dir);
    }

    if (!$node instanceof Element || $node->getTag() !== 'a') {
      return;
    }

    $attributes = $node->getAttributes();
    $href = $attributes->getAttribute('href') ?? '';

    if (UrlHelper::isExternal($href)) {
      return;
    }

    $relative_pathname = $source_dir . \DIRECTORY_SEPARATOR . $href;
    $pathname = PathHelper::normalizePath($relative_pathname);
    $this->prepareLink($node, $pathname);
  }

  /**
   * {@selfdoc}
   */
  private function prepareLink(Element $node, string $pathname): void {
    if (\is_dir($pathname)) {
      $this->prepareExternalContentLink($node, $pathname);
    }
    elseif (\is_file($pathname)) {
      $this->prepareInternalFileLink($node, $pathname);
    }
  }

  /**
   * Prepare a link to the directory inside the external content.
   *
   * Since referencing the directory from the external content means that we
   * have no related content on the website, we should replace it with the
   * content repository URL.
   *
   * Most likely, it is a reference to some directory containing examples.
   */
  private function prepareExternalContentLink(Element $node, string $pathname): void {
    $external_content_dir = Settings::get('external_content_directory');
    $repository_url = Settings::get('external_content_repository_url');
    $url = \str_replace(
      search: $external_content_dir,
      // Since GitHub is requiring that part, it is forced here.
      // @todo Think how it can be improved to handle without hardcoding.
      replace: "$repository_url/tree/main",
      subject: $pathname,
    );

    $attributes = $node->getAttributes();
    $attributes->setAttribute('href', $url);
  }

  /**
   * Assigning labels to internal links to facilitate content search.
   *
   * This logic is implemented in the loader as it is more efficient to perform
   * the operation once. Once the content has been synchronized, the source
   * directory may be deleted and this operation will fail. Therefore, all
   * necessary information must be extracted during the loading process, when
   * all sources are accessible.
   *
   * @see \Drupal\niklan\Builder\ExternalContent\RenderArray\Link
   */
  private function prepareInternalFileLink(Element $node, string $pathname): void {
    $attributes = $node->getAttributes();
    // If the link points to a file that already exists in the directory with
    // the original contents, then disable this link and add additional
    // information.
    //
    // Thanks to the path name hash, the content located at this specific path
    // will be retrieved from the database when the link is rendered. This
    // allows for the correct URL to be displayed.
    $attributes->setAttribute('href', '#');
    $attributes->setAttribute('data-selector', 'niklan:external-link');
    $attributes->setAttribute('data-pathname-md5', \md5($pathname));
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

  /**
   * {@selfdoc}
   */
  private function replaceMediaVideos(NodeInterface $node, string $source_dir): void {
    foreach ($node->getChildren() as $child) {
      \assert($child instanceof NodeInterface);
      $this->replaceMediaVideos($child, $source_dir);
    }

    if (!$node instanceof Video) {
      return;
    }

    $src = $node->src;

    if (!UrlHelper::isExternal($src)) {
      $src = "$source_dir/$src";
    }

    $media = $this->contentAssetManager->syncWithMedia($src);

    if (!$media instanceof MediaInterface) {
      return;
    }

    $new_node = new DrupalMedia(
      type: 'video',
      uuid: $media->uuid(),
      data: new Data([
        'title' => $node->title,
      ]),
    );

    $node->getParent()->replaceNode($node, $new_node);
  }

}
