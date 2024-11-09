<?php

declare(strict_types=1);

namespace Drupal\niklan\Tag\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Node\Entity\NodeInterface;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class Tag implements ContainerInjectionInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
    );
  }

  private function buildItems(TermInterface $taxonomy_term): array {
    $ids = $this
      ->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('field_tags', $taxonomy_term->id())
      ->sort('created', 'DESC')
      ->pager()
      ->execute();
    $view_builder = $this->entityTypeManager->getViewBuilder('node');

    return \array_map(
      // @phpstan-ignore-next-line
      callback: static fn (NodeInterface $node): array => $view_builder->view(
        entity: $node,
        view_mode: 'teaser',
      ),
      array: $this
        ->entityTypeManager
        ->getStorage('node')
        ->loadMultiple($ids),
    );
  }

  public function __invoke(TermInterface $taxonomy_term): array {
    return [
      '#title' => new TranslatableMarkup('Publications with the @name tag', [
        '@name' => $taxonomy_term->label(),
      ]),
      '#theme' => 'niklan_blog_list',
      '#items' => $this->buildItems($taxonomy_term),
      '#pager' => [
        '#type' => 'pager',
        '#quantity' => 4,
      ],
      '#cache' => [
        'tags' => ['node_list'],
      ],
    ];
  }

}
