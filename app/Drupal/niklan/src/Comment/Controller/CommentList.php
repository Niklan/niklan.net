<?php

declare(strict_types=1);

namespace Drupal\niklan\Comment\Controller;

use Drupal\comment\CommentInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class CommentList implements ContainerInjectionInterface {

  protected const int LIMIT = 10;

  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
    );
  }

  protected function prepareResults(): array {
    $items = [];
    $view_builder = $this->entityTypeManager->getViewBuilder('comment');

    foreach ($this->load() as $comment) {
      \assert($comment instanceof CommentInterface);
      $items[] = $view_builder->view($comment, 'teaser');
    }

    return $items;
  }

  protected function load(): array {
    $ids = $this->getEntityIds();

    return $ids
      ? $this->entityTypeManager->getStorage('comment')->loadMultiple($ids)
      : [];
  }

  protected function getEntityIds(): array {
    $query = $this
      ->entityTypeManager
      ->getStorage('comment')
      ->getQuery()
      ->accessCheck(FALSE);

    $query
      ->condition('comment_type', 'comment_node_blog_entry')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->pager(self::LIMIT);

    return $query->execute();
  }

  public function __invoke(): array {
    return [
      '#theme' => 'niklan_comment_list',
      '#comments' => $this->prepareResults(),
      '#pager' => [
        '#type' => 'pager',
        '#quantity' => 3,
      ],
      '#cache' => [
        'tags' => ['comment_list'],
      ],
    ];
  }

}
