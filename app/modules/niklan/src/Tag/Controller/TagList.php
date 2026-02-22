<?php

declare(strict_types=1);

namespace Drupal\niklan\Tag\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\app_contract\Contract\Tag\TagUsageStatistics;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class TagList implements ContainerInjectionInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
    private TagUsageStatistics $statistics,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
      $container->get(TagUsageStatistics::class),
    );
  }

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
      '#theme' => 'niklan_tag_list',
      '#items' => $this->buildItems(),
    ];
  }

}
