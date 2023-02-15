<?php declare(strict_types = 1);

namespace Drupal\niklan\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\node\NodeInterface;
use Drupal\node\NodeStorageInterface;
use Drupal\node\NodeViewBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides controller for blog list route.
 */
final class BlogController implements ContainerInjectionInterface {

  /**
   * The renderer.
   */
  protected RendererInterface $renderer;

  /**
   * The node storage.
   */
  protected NodeStorageInterface $nodeStorage;

  /**
   * The node view builder.
   */
  protected NodeViewBuilder $nodeViewBuilder;

  /**
   * The amount of articles per page.
   */
  protected int $limit = 10;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $entity_type_manager = $container->get('entity_type.manager');

    $instance = new self();
    $instance->nodeStorage = $entity_type_manager->getStorage('node');
    $instance->nodeViewBuilder = $entity_type_manager->getViewBuilder('node');
    $instance->renderer = $container->get('renderer');

    return $instance;
  }

  /**
   * Builds the response.
   */
  public function list(): array {
    return [
      '#theme' => 'niklan_blog_list',
      '#items' => $this->getItems(),
      '#pager' => $this->buildPager(),
      '#cache' => [
        'tags' => ['node_list'],
      ],
    ];
  }

  /**
   * Builds list of entities.
   *
   * @return array
   *   An array with blog post render arrays.
   */
  protected function getItems(): array {
    $items = [];

    foreach ($this->load() as $node) {
      $items[] = $this->nodeViewBuilder->view($node, 'teaser');
    }

    return $items;
  }

  /**
   * Loads entities.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array with blog articles.
   */
  protected function load(): array {
    $ids = $this->getEntityIds();
    return $this->nodeStorage->loadMultiple($ids);
  }

  /**
   * Gets entity ids.
   *
   * @return array
   *   The list of ids.
   */
  protected function getEntityIds(): array {
    $query = $this->nodeStorage->getQuery()->accessCheck(FALSE);
    $query
      ->condition('type', 'blog_entry')
      ->condition('status', NodeInterface::PUBLISHED)
      ->sort('created', 'DESC');

    if ($this->limit) {
      $query->pager($this->limit);
    }

    return $query->execute();
  }

  /**
   * Builds pager element.
   *
   * @return array
   *   The render array with pager.
   */
  protected function buildPager(): array {
    return [
      '#type' => 'pager',
      '#quantity' => 4,
    ];
  }

}
