<?php

declare(strict_types=1);

namespace Drupal\app_tag\Controller;

use Drupal\app_contract\Contract\Tag\TagUsageStatistics;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\taxonomy\TermInterface;

final readonly class TagList {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
    private TagUsageStatistics $statistics,
  ) {}

  private function buildItems(): array {
    $ids = \array_keys($this->statistics->usage());
    $view_builder = $this->entityTypeManager->getViewBuilder('taxonomy_term');

    return \array_map(
      callback: static fn (TermInterface $term): array => $view_builder->view(
        entity: $term,
        view_mode: 'teaser',
      ),
      array: $this
        ->entityTypeManager
        ->getStorage('taxonomy_term')
        ->loadMultiple($ids),
    );
  }

  public function __invoke(): array {
    return [
      '#theme' => 'app_tag_list',
      '#items' => $this->buildItems(),
    ];
  }

}
