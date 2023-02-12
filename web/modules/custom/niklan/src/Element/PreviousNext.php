<?php

declare(strict_types = 1);

namespace Drupal\niklan\Element;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element\RenderElement;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an element that prints links to previous and next articles.
 *
 * @RenderElement("niklan_previous_next")
 */
final class PreviousNext extends RenderElement implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getInfo(): array {
    return [
      '#theme' => 'niklan_previous_next',
      '#entity' => NULL,
      '#pre_render' => [
        [$this, 'prepareLinks'],
      ],
    ];
  }

  /**
   * Prepares links for the template.
   *
   * @param array $element
   *   The current element.
   *
   * @return array
   *   The updated element.
   */
  public function prepareLinks(array $element): array {
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $element['#entity'];
    $cache = new CacheableMetadata();
    $cache->addCacheableDependency($entity);

    if ($previous_entity = $this->findPrevious($entity)) {
      $element['#previous_url'] = $previous_entity->toUrl()->toString();
      $element['#previous_label'] = $previous_entity->label();
      $cache->addCacheableDependency($previous_entity);
    }

    if ($next_entity = $this->findNext($entity)) {
      $element['#next_url'] = $next_entity->toUrl()->toString();
      $element['#next_label'] = $next_entity->label();
      $cache->addCacheableDependency($next_entity);
    }

    $cache->applyTo($element);

    return $element;
  }

  /**
   * Looking for previously published article.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The current entity.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface|null
   *   The previous to current entity.
   */
  protected function findPrevious(ContentEntityInterface $entity): ?ContentEntityInterface {
    $storage = $this->entityTypeManager->getStorage($entity->getEntityTypeId());
    $id = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', $entity->bundle())
      ->condition('created', $entity->getCreatedTime(), '>')
      ->range(0, 1)
      ->sort('created')
      ->execute();

    return $id ? $storage->load(\reset($id)) : NULL;
  }

  /**
   * Looking for next published article.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The current entity.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface|null
   *   The previous to current entity.
   */
  protected function findNext(ContentEntityInterface $entity): ?ContentEntityInterface {
    $storage = $this->entityTypeManager->getStorage(
        $entity->getEntityTypeId(),
    );
    $id = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', $entity->bundle())
      ->condition('created', $entity->getCreatedTime(), '<')
      ->range(0, 1)
      ->sort('created', 'DESC')
      ->execute();

    return $id ? $storage->load(\reset($id)) : NULL;
  }

}
