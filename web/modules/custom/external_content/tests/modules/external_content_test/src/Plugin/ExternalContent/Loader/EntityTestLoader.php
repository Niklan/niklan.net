<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Plugin\ExternalContent\Loader;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\external_content\Data\ExternalContent;
use Drupal\external_content\Plugin\ExternalContent\Loader\LoaderPlugin;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a loader for 'entity_test' entity.
 *
 * @ExternalContentLoader(
 *   id = "entity_test_loader",
 *   label = @Translation("Entity test loader"),
 * )
 */
final class EntityTestLoader extends LoaderPlugin implements ContainerFactoryPluginInterface {

  /**
   * The entity test storage.
   */
  protected EntityStorageInterface $entityTestStorage;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->entityTestStorage = $container
      ->get('entity_type.manager')
      ->getStorage('entity_test');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function load(ExternalContent $external_content): void {
    if (!$external_content->hasTranslation('en')) {
      return;
    }

    $translation = $external_content->getTranslation('en');
    $parameters = $translation->getParams();

    $entity = $this->prepareDestinationEntity($external_content->id());

    if (!$entity instanceof EntityTest) {
      return;
    }

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
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The testing entity. NULL if error occurs while loading.
   */
  protected function prepareDestinationEntity(string $external_id): ?EntityInterface {
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

    $entity = $this->entityTestStorage->create();
    \assert($entity instanceof EntityTest);
    $entity->set('external_id', $external_id);

    return $entity;
  }

}
