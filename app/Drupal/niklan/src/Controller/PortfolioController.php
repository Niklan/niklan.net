<?php

declare(strict_types=1);

namespace Drupal\niklan\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\node\NodeInterface;
use Drupal\node\NodeStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class PortfolioController implements ContainerInjectionInterface {

  protected NodeStorageInterface $nodeStorage;
  protected EntityViewBuilderInterface $nodeViewBuilder;

  #[\Override]
  public static function create(ContainerInterface $container): self {
    $entity_type_manager = $container->get('entity_type.manager');

    $instance = new self();
    $instance->nodeStorage = $entity_type_manager->getStorage('node');
    $instance->nodeViewBuilder = $entity_type_manager->getViewBuilder('node');

    return $instance;
  }

  public function list(): array {
    return [
      '#theme' => 'niklan_portfolio_list',
      '#items' => $this->buildItems(),
    ];
  }

  protected function buildItems(): array {
    $items = [];

    foreach ($this->load() as $item) {
      $items[] = $this->nodeViewBuilder->view($item, 'teaser');
    }

    return $items;
  }

  /**
   * @return \Drupal\node\NodeInterface[]
   *   The nodes.
   */
  protected function load(): array {
    return $this->nodeStorage->loadMultiple($this->getEntityIds());
  }

  protected function getEntityIds(): array {
    $query = $this->nodeStorage->getQuery()->accessCheck(FALSE);
    $query
      ->condition('type', 'portfolio')
      ->condition('status', NodeInterface::PUBLISHED)
      ->sort('field_date', 'DESC');

    return $query->execute();
  }

}
