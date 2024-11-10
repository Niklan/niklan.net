<?php

declare(strict_types=1);

namespace Drupal\niklan\Portfolio\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class PortfolioList implements ContainerInjectionInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
    );
  }

  protected function buildItems(): array {
    $view_builder = $this->entityTypeManager->getViewBuilder('node');
    $items = [];

    foreach ($this->load() as $item) {
      $items[] = $view_builder->view($item, 'teaser');
    }

    return $items;
  }

  /**
   * @return \Drupal\node\NodeInterface[]
   *   The nodes.
   */
  protected function load(): array {
    return $this
      ->entityTypeManager
      ->getStorage('node')
      ->loadMultiple($this->getEntityIds());
  }

  protected function getEntityIds(): array {
    return $this
      ->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'portfolio')
      ->condition('status', NodeInterface::PUBLISHED)
      ->sort('field_date', 'DESC')
      ->execute();
  }

  public function __invoke(): array {
    return [
      '#theme' => 'niklan_portfolio_list',
      '#items' => $this->buildItems(),
    ];
  }

}
