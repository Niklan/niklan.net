<?php declare(strict_types = 1);

namespace Drupal\niklan\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides controller for blog list route.
 */
final class BlogController implements ContainerInjectionInterface {

  /**
   * The amount of articles per page.
   */
  protected int $limit = 10;

  /**
   * Constructs a new BlogController instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected RendererInterface $renderer,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity_type.manager'),
      $container->get('renderer'),
    );
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
      $items[] = $this
        ->entityTypeManager
        ->getViewBuilder('node')
        ->view($node, 'teaser');
    }

    return $items;
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

  /**
   * Loads entities.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array with blog articles.
   */
  protected function load(): array {
    $ids = $this->getEntityIds();

    return $this->entityTypeManager->getStorage('node')->loadMultiple($ids);
  }

  /**
   * Gets entity ids.
   *
   * @return array
   *   The list of ids.
   */
  protected function getEntityIds(): array {
    $query = $this
      ->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'blog_entry')
      ->condition('status', NodeInterface::PUBLISHED)
      ->sort('created', 'DESC');

    if ($this->limit) {
      $query->pager($this->limit);
    }

    return $query->execute();
  }

}
