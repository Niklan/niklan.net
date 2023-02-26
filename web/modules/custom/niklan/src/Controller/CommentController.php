<?php declare(strict_types = 1);

namespace Drupal\niklan\Controller;

use Drupal\comment\CommentInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a custom controller for comment related routes.
 */
final class CommentController implements ContainerInjectionInterface {

  /**
   * The amount of comments per page.
   */
  protected const LIMIT = 10;

  /**
   * Constructs a new CommentController instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Builds a comment list.
   *
   * @return array
   *   An array with contents.
   */
  public function list(): array {
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

  /**
   * Prepares results.
   *
   * @return array
   *   An array with result items.
   */
  protected function prepareResults(): array {
    $items = [];
    $view_builder = $this->entityTypeManager->getViewBuilder('comment');

    foreach ($this->load() as $comment) {
      \assert($comment instanceof CommentInterface);
      $items[] = $view_builder->view($comment, 'teaser');
    }

    return $items;
  }

  /**
   * Load entities.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array with entities.
   */
  protected function load(): array {
    $ids = $this->getEntityIds();

    return $ids
      ? $this->entityTypeManager->getStorage('comment')->loadMultiple($ids)
      : [];
  }

  /**
   * Gets entity ids.
   *
   * @return array
   *   An array of comment IDs.
   */
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

}
