<?php declare(strict_types = 1);

namespace Drupal\niklan\Loader\ExternalContent;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Loader\LoaderResultInterface;
use Drupal\external_content\Contract\Node\NodeInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\ContentBundle;
use Drupal\external_content\Data\ContentVariation;
use Drupal\external_content\Data\LoaderResult;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Node\Html\Element;
use Drupal\media\MediaInterface;
use Drupal\niklan\Asset\ContentAssetManager;
use Drupal\niklan\Entity\Node\BlogEntryInterface;
use Drupal\niklan\Helper\PathHelper;
use Drupal\niklan\Node\ExternalContent\DrupalMedia;
use Drupal\taxonomy\TermStorageInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class Blog implements LoaderInterface, EnvironmentAwareInterface, ContainerAwareInterface {

  /**
   * {@selfdoc}
   */
  private ContainerInterface $container;

  /**
   * {@selfdoc}
   */
  private EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function load(ContentBundle $bundle): LoaderResultInterface {
    $blog_entry = $this->findBlogEntry($bundle->id);

    foreach ($bundle->getByAttribute('language') as $content_variation) {
      \assert($content_variation instanceof ContentVariation);
      // Switch the content language to be the same as variation.
      $langcode = $content_variation->attributes->getAttribute('language');
      $blog_entry = $blog_entry->getTranslation($langcode);
      $this->processBlogEntryVariation($blog_entry, $content_variation);
    }

    // @todo Add some checks to avoid unnecessary saving.
    $blog_entry->save();

    return LoaderResult::entity(
      entity_type_id: $blog_entry->getEntityTypeId(),
      entity_id: $blog_entry->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setContainer(?ContainerInterface $container): void {
    $this->container = $container;
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
  private function findBlogEntry(string $external_id): ?BlogEntryInterface {
    $storage = $this->getEntityTypeManager()->getStorage('node');

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
  private function getEntityTypeManager(): EntityTypeManagerInterface {
    return $this->container->get('entity_type.manager');
  }

  /**
   * {@selfdoc}
   */
  private function getSerializer(): SerializerInterface {
    $serializer = $this->container->get(SerializerInterface::class);
    $serializer->setEnvironment($this->environment);

    return $serializer;
  }

  /**
   * {@selfdoc}
   */
  private function processBlogEntryVariation(BlogEntryInterface $blog_entry, ContentVariation $content_variation): void {
    $content = $content_variation->content;
    $source_info = $content->getData()->get('source');
    $front_matter = $source_info['front_matter'];
    $source_dir = \dirname($source_info['pathname']);

    $created = DrupalDateTime::createFromFormat(
      format: DateTimeItemInterface::DATETIME_STORAGE_FORMAT,
      time: $front_matter['created'],
    );
    $updated = DrupalDateTime::createFromFormat(
      format: DateTimeItemInterface::DATETIME_STORAGE_FORMAT,
      time: $front_matter['updated'],
    );

    $blog_entry->setTitle($front_matter['title']);
    $blog_entry->setCreatedTime($created->getTimestamp());
    $blog_entry->setChangedTime($updated->getTimestamp());
    $blog_entry->set('body', ['value' => $front_matter['description']]);
    $this->processTags($blog_entry, $front_matter);
    $this->processPromo($blog_entry, $content, $source_dir);
    $this->processAttachments($blog_entry, $content);
    $this->replaceMediaNodes($content, $source_dir);
    $this->prepareInternalLinks($content, $source_dir);

    $additional_info = [
      // For internal links. MD5 is used instead clear value for a smaller size
      // of the stored data.
      'pathname_md5' => \md5($source_info['pathname']),
    ];

    $blog_entry->set('external_content', [
      'value' => $this->getSerializer()->normalize($content),
      'environment_plugin_id' => $this
        ->environment
        ->getConfiguration()
        ->get('environment_plugin_id'),
      'data' => \json_encode($additional_info),
    ]);
  }

  /**
   * {@selfdoc}
   */
  private function processTags(BlogEntryInterface $blog_entry, array $front_matter): void {
    $blog_entry->set('field_tags', NULL);

    if (!\array_key_exists('tags', $front_matter) || !\is_array($front_matter['tags'])) {
      return;
    }

    $term_storage = $this->getEntityTypeManager()->getStorage('taxonomy_term');
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
  private function processPromo(BlogEntryInterface $blog_entry, Content $content, string $source_dir): void {
    $blog_entry->set('field_media_image', NULL);
    $source_data = $content->getData()->get('source');

    if (!isset($source_data['pathname']) || !isset($source_data['front_matter']['promo'])) {
      return;
    }

    $promo_asset = $source_data['front_matter']['promo'];
    $promo_pathname = "$source_dir/$promo_asset";

    if (!\file_exists($promo_pathname)) {
      return;
    }

    $asset_manager = $this->container->get(ContentAssetManager::class);
    $media = $asset_manager->syncWithMedia($promo_pathname);

    if (!$media instanceof MediaInterface) {
      return;
    }

    $blog_entry->set('field_media_image', ['target_id' => $media->id()]);
  }

  /**
   * {@selfdoc}
   */
  private function processAttachments(BlogEntryInterface $blog_entry, Content $content): void {
    $blog_entry->set('field_media_attachments', NULL);
    $source_data = $content->getData()->get('source');

    if (!isset($source_data['pathname']) || !isset($source_data['front_matter']['attachments'])) {
      return;
    }

    $asset_manager = $this->container->get(ContentAssetManager::class);
    $source_dir = \dirname($content->getData()->get('source')['pathname']);

    foreach ($source_data['front_matter']['attachments'] as $attachment) {
      $attachment_pathname = "$source_dir/{$attachment['path']}";
      $media = $asset_manager->syncWithMedia($attachment_pathname);

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

    $asset_manager = $this->container->get(ContentAssetManager::class);
    $media = $asset_manager->syncWithMedia($src);

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

}
