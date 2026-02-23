<?php

declare(strict_types=1);

namespace Drupal\app_portfolio\Controller;

use Drupal\app_portfolio\Repository\PortfolioSettings;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;

final readonly class PortfolioList {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
    private PortfolioSettings $settings,
  ) {}

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
      '#theme' => 'app_portfolio_list',
      '#description' => [
        '#type' => 'processed_text',
        '#text' => $this->settings->getDescription(),
        '#format' => PortfolioSettings::TEXT_FORMAT,
      ],
      '#items' => $this->buildItems(),
    ];
  }

}
