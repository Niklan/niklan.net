<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Plugin\ExternalContent\Loader;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\external_content\Dto\ExternalContent;
use Drupal\external_content\Dto\ExternalContentCollection;
use Drupal\external_content\Plugin\ExternalContent\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a loader for 'entity_test' entity.
 *
 * @ExternalContentLoader(
 *   id = "entity_test_loader",
 *   label = @Translation("Entity test loader"),
 * )
 */
final class EntityTestLoader implements LoaderInterface, ContainerFactoryPluginInterface {

  /**
   * The entity test storage.
   */
  protected ContentEntityStorageInterface $entityTestStorage;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self();
    $instance->entityTestStorage = $container
      ->get('entity_type.manager')
      ->getStorage('entity_test');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function load(ExternalContentCollection $external_content_collection): void {
    foreach ($external_content_collection as $external_content) {
      $this->loadExternalContent($external_content);
    }
  }

  /**
   * Loads a single external content.
   *
   * @param \Drupal\external_content\Dto\ExternalContent $external_content
   *   The external content item.
   */
  protected function loadExternalContent(ExternalContent $external_content): void {
    if (!$external_content->hasTranslation('en')) {
      return;
    }

    $translation = $external_content->getTranslation('en');
    $parameters = $translation->getParams();

    $entity = $this->prepareDestinationEntity($external_content->id());
    if ($parameters->has('title')) {
      $entity->setName($parameters->get('title'));
    }
    $entity->set('external_content', $external_content);
    $entity->save();
  }

  /**
   * Prepares a destination entity.
   *
   * @param string $external_id
   *   The external ID.
   */
  protected function prepareDestinationEntity(string $external_id): EntityTest {
    $ids = $this
      ->entityTestStorage
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('external_id', $external_id)
      ->range(0, 1)
      ->execute();

    if ($ids) {
      return $this->entityTestStorage->load(\reset($ids));
    }

    /** @var \Drupal\entity_test\Entity\EntityTest $entity */
    $entity = $this->entityTestStorage->create();
    $entity->set('external_id', $external_id);

    return $entity;
  }

}
