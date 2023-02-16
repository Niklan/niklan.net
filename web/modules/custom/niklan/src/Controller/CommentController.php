<?php declare(strict_types = 1);

namespace Drupal\niklan\Controller;

use Drupal\comment\CommentInterface;
use Drupal\comment\CommentStorageInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
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
   * The comment storage.
   */
  protected CommentStorageInterface $commentStorage;

  /**
   * The comment view builder.
   */
  protected EntityViewBuilderInterface $commentViewBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $entity_type_manager = $container->get('entity_type.manager');

    $instance = new self();
    $instance->commentStorage = $entity_type_manager->getStorage('comment');
    $instance->commentViewBuilder = $entity_type_manager
      ->getViewBuilder('comment');

    return $instance;
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
        'tags' => [
          'comment_list',
        ],
        'context' => [
          'url.query_args.pagers:0',
        ],
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

    foreach ($this->load() as $comment) {
      \assert($comment instanceof CommentInterface);
      // Render separately to create flat array.
      $items[] = $this->commentViewBuilder->view($comment, 'teaser');
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

    return $ids ? $this->commentStorage->loadMultiple($ids) : [];
  }

  /**
   * Gets entity ids.
   *
   * @return array
   *   An array of comment IDs.
   */
  protected function getEntityIds(): array {
    $query = $this->commentStorage->getQuery()->accessCheck(FALSE);
    $query
      ->condition('comment_type', 'comment_node_blog_entry')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->pager(self::LIMIT);

    return $query->execute();
  }

}
