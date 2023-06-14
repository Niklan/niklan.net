<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\ExternalContent\Loader;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\external_content\Data\ExternalContent;
use Drupal\external_content\Data\SourceFileParams;
use Drupal\external_content\Plugin\ExternalContent\Loader\LoaderPlugin;
use Drupal\niklan\Entity\Node\BlogEntryInterface;
use Drupal\node\NodeStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a loader for 'blog_entry' entity.
 *
 * @ExternalContentLoader(
 *   id = "content",
 *   label = @Translation("Blog Entry content"),
 * )
 *
 * @ingroup content_sync
 */
final class ContentLoader extends LoaderPlugin implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function load(ExternalContent $external_content): void {
    // Only Russian language is now expected/supported.
    if (!$external_content->hasTranslation('ru')) {
      return;
    }

    $content = $this->prepareContent($external_content->id());
    $parsed_content = $external_content->getTranslation('ru');

    $this->syncFrontMatter($content, $parsed_content->getParams());
    $content->setExternalContent($parsed_content);

    $content->save();
  }

  /**
   * Prepares destination entity.
   *
   * @param string $external_id
   *   The external content ID.
   *
   * @return \Drupal\niklan\Entity\Node\BlogEntryInterface
   *   The created/existing content entity.
   */
  protected function prepareContent(string $external_id): BlogEntryInterface {
    $node_storage = $this->entityTypeManager->getStorage('node');
    \assert($node_storage instanceof NodeStorageInterface);

    $entity_ids = $node_storage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'blog_entry')
      ->condition('external_id', $external_id)
      ->execute();
    $entity_id = \array_shift($entity_ids);

    if ($entity_id) {
      $content = $node_storage->load($entity_id);
      \assert($content instanceof BlogEntryInterface);

      return $content;
    }

    // If not found, create a new one.
    $content = $node_storage->create(['type' => 'blog_entry']);
    \assert($content instanceof BlogEntryInterface);
    // Make new content unpublished by default. It will require manual check and
    // publication.
    $content->setUnpublished();
    $content->setExternalId($external_id);

    return $content;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Syncs Front Matter.
   *
   * @param \Drupal\niklan\Entity\Node\BlogEntryInterface $content
   *   The content entity.
   * @param \Drupal\external_content\Data\SourceFileParams $params
   *   The Front Matter params.
   */
  protected function syncFrontMatter(BlogEntryInterface $content, SourceFileParams $params): void {
    $content->setTitle($params->get('title'));

    if ($params->has('created')) {
      $created = DrupalDateTime::createFromFormat(
        DateTimeItemInterface::DATETIME_STORAGE_FORMAT,
        $params->get('created'),
      );
      $content->setCreatedTime($created->getTimestamp());
    }

    if ($params->has('updated')) {
      $updated = DrupalDateTime::createFromFormat(
        DateTimeItemInterface::DATETIME_STORAGE_FORMAT,
        $params->get('updated'),
      );
      $content->setChangedTime($updated->getTimestamp());
    }

    $content->set('body', NULL);

    if ($params->has('description')) {
      $content->set('body', [
        'value' => $params->get('description'),
        'format' => 'markdown',
      ]);
    }

    // @todo Add support for 'attachments', 'promo', 'tags'.
  }

}
