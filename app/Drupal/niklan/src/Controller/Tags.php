<?php

declare(strict_types=1);

namespace Drupal\niklan\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\niklan\Contract\Utility\TagStatisticsInterface;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class Tags implements ContainerInjectionInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
    private TagStatisticsInterface $statistics,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
      $container->get(TagStatisticsInterface::class),
    );
  }

  private function buildItems(): array {
    $ids = \array_keys($this->statistics->getBlogEntryUsage());
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
