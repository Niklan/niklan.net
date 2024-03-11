<?php declare(strict_types = 1);

namespace Drupal\niklan\Loader\ExternalContent;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\ExternalContent\ExternalContentManagerInterface;
use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Data\IdentifiedSource;
use Drupal\external_content\Data\IdentifiedSourceBundle;
use Drupal\external_content\Data\LoaderResult;
use Drupal\external_content\Node\Html\Element;
use Drupal\media\MediaInterface;
use Drupal\niklan\Asset\ContentAssetManager;
use Drupal\niklan\Entity\Node\BlogEntryInterface;
use Drupal\niklan\Exception\InvalidContentSource;
use Drupal\niklan\Helper\PathHelper;
use Drupal\niklan\Node\ExternalContent\DrupalMedia;
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
  ) {}

  /**
   * {@inheritdoc}
   */
  public function load(IdentifiedSourceBundle $bundle): LoaderResult {
    $blog_entry = $this->findBlogEntry($bundle->id);

    foreach ($bundle->getAllWithAttribute('language')->sources() as $identified_source) {
      // Switch the content language to be the same as variation.
      $language = $identified_source->attributes->getAttribute('language');
      $blog_entry = $blog_entry->getTranslation($language);
      $this->syncBlogEntryVariation($blog_entry, $identified_source);
    }

    // @todo Add some checks to avoid unnecessary saving.
    $blog_entry->save();

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
    $normalized = $this
      ->externalContentManager
      ->getSerializerManager()
      ->normalize($content, $this->environment);

    return;

    $this->replaceMediaNodes($content, $source_dir);
    $this->prepareInternalLinks($content, $source_dir);

    $additional_info = [
      // For internal links. MD5 is used instead clear value for a smaller size
      // of the stored data.
      'pathname_md5' => \md5($source_info['pathname']),
    ];

    $blog_entry->set('external_content', [
      'value' => $this->getSerializer()->normalize($content),
      'environment_id' => 'blog',
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
  private function replaceMediaNodes(NodeInterface $node, string $source_dir): void {
    foreach ($node->getChildren() as $child) {
      \assert($node instanceof NodeInterface);
      $this->replaceMediaNodes($child, $source_dir);
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
      $media->uuid(),
      $node->getAttributes()->getAttribute('alt'),
      $node->getAttributes()->getAttribute('title'),
    );
    $node->getRoot()->replaceNode($node, $new_node);
  }

  /**
   * {@selfdoc}
   *
   * This logic done in loader because it better to be done once. After content
   * is synced, the source directory can be removed and this logic would fail.
   * This is why the all required information extracted during loading when
   * all sources are exist.
   */
  private function prepareInternalLinks(NodeInterface $node, string $source_dir): void {
    foreach ($node->getChildren() as $child) {
      $this->prepareInternalLinks($child, $source_dir);
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

    // Only if the resulted pathname is existing we detected referenced to
    // another content inside the source.
    if (!\file_exists($pathname)) {
      return;
    }

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

}
