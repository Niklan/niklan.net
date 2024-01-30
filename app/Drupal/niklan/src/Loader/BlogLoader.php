<?php declare(strict_types = 1);

namespace Drupal\niklan\Loader;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Loader\LoaderInterface;
use Drupal\external_content\Contract\Loader\LoaderResultInterface;
use Drupal\external_content\Contract\Serializer\SerializerInterface;
use Drupal\external_content\Data\ContentBundle;
use Drupal\external_content\Data\ContentVariation;
use Drupal\external_content\Data\LoaderResult;
use Drupal\external_content\Node\Content;
use Drupal\niklan\Entity\Node\BlogEntry;
use Drupal\niklan\Entity\Node\BlogEntryInterface;
use Drupal\node\NodeStorageInterface;
use Drupal\taxonomy\TermStorageInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
final class BlogLoader implements LoaderInterface, EnvironmentAwareInterface, ContainerAwareInterface {

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

    // If it's not found, stop processing. The new content is not created during
    // sync at this point, and maybe never will be.
    if (!$blog_entry instanceof BlogEntryInterface) {
      return LoaderResult::ignore();
    }

    foreach ($bundle->getByAttribute('language') as $content_variation) {
      \assert($content_variation instanceof ContentVariation);
      // Switch the content language to be the same as variation.
      $langcode = $content_variation->attributes->getAttribute('language');
      $blog_entry = $blog_entry->getTranslation($langcode);
      $this->processBlogEntryVariation($blog_entry, $content_variation);
    }

    // @todo Consider add some kind of sync command version and check it as
    //   well. The content may be not changed, but the logic can be changed and
    //   it should be handled here.
    if ($blog_entry->getChangedTime() !== $blog_entry->original->getChangedTime()) {
      $blog_entry->save();
    }

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
  private function findBlogEntry(string $external_id): ?BlogEntry {
    $node_storage = $this->getEntityTypeManager()->getStorage('node');
    \assert($node_storage instanceof NodeStorageInterface);

    $ids = $node_storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'blog_entry')
      ->condition('external_id', $external_id)
      ->range(0, 1)
      ->execute();

    if (!$ids) {
      return NULL;
    }

    return $node_storage->load(\reset($ids));
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
   *
   * @todo
   *   - [x] Created
   *   - [x] Updated
   *   - [x] Title
   *   - [x] Description
   *   - [ ] Promo
   *   - [x] Tags
   */
  private function processBlogEntryVariation(BlogEntryInterface $blog_entry, ContentVariation $content_variation): void {
    $content = $content_variation->content;
    $front_matter = $content->getData()->get('front_matter');
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
    $this->processPromo($blog_entry, $content);

    // @todo Loop over content and replace asset nodes with a new one that
    //   uses Drupal internal Media IDs/URIs.
    $normalized = $this
      ->getSerializer()
      ->normalize($content_variation->content);
    $blog_entry->set('external_content', [
      'value' => $normalized,
      'environment_plugin_id' => $this
        ->environment
        ->getConfiguration()
        ->get('environment_plugin_id'),
    ]);
  }

  /**
   * {@selfdoc}
   */
  private function processTags(BlogEntryInterface $blog_entry, array $front_matter): void {
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
  private function processPromo(BlogEntryInterface $blog_entry, Content $content): void {
    $source_data = $content->getData()->get('source');

    if (!isset($source_data['pathname']) || !isset($source_data['front_matter']['promo'])) {
      return;
    }

    $source_dir = \dirname($content->getData()->get('source')['pathname']);
    $promo_asset = $source_data['front_matter']['promo'];
    $promo_pathname = "$source_dir/$promo_asset";

    if (!\file_exists($promo_pathname)) {
      return;
    }

    // @todo Find media by a checksum.
  }

}
