<?php declare(strict_types = 1);

namespace Drupal\niklan\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\niklan\Helper\TagStatistics;
use Drupal\node\NodeInterface;
use Drupal\node\NodeStorageInterface;
use Drupal\node\NodeViewBuilder;
use Drupal\taxonomy\TermInterface;
use Drupal\taxonomy\TermStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides controller for tags.
 */
final class TagController implements ContainerInjectionInterface {

  /**
   * The tag statistics helper.
   */
  protected TagStatistics $tagStatistics;

  /**
   * The term view builder.
   */
  protected EntityViewBuilderInterface $termViewBuilder;

  /**
   * The term storage.
   */
  protected TermStorageInterface $termStorage;

  /**
   * The node storage.
   */
  protected NodeStorageInterface $nodeStorage;

  /**
   * The node view builder.
   */
  protected NodeViewBuilder $nodeViewBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $entity_type_manager = $container->get('entity_type.manager');

    $instance = new self();
    $instance->tagStatistics = $container->get('niklan.helper.tag_statistics');
    $instance->termViewBuilder = $entity_type_manager
      ->getViewBuilder('taxonomy_term');
    $instance->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $instance->nodeStorage = $entity_type_manager->getStorage('node');
    $instance->nodeViewBuilder = $entity_type_manager->getViewBuilder('node');

    return $instance;
  }

  /**
   * Builds page with all tags.
   *
   * @return array
   *   An array with page content.
   */
  public function collection(): array {
    $tag_ids = \array_keys($this->tagStatistics->getBlogEntryUsage());
    $terms = $this->termStorage->loadMultiple($tag_ids);

    return [
      '#theme' => 'niklan_tag_list',
      '#items' => \array_map(
        fn ($term) => $this->termViewBuilder->view($term, 'teaser'),
        $terms,
      ),
    ];
  }

  /**
   * Builds a single tag page.
   *
   * @param \Drupal\taxonomy\TermInterface $term
   *   The tag entity.
   *
   * @return array
   *   An array with page contents.
   */
  public function page(TermInterface $term): array {
    return [
      '#theme' => 'niklan_tag_page',
      '#items' => $this->getBlogPosts($term),
      '#pager' => [
        '#type' => 'pager',
        '#quantity' => 4,
      ],
    ];
  }

  /**
   * Builds list of entities.
   *
   * @param \Drupal\taxonomy\TermInterface $term
   *   The term entity.
   *
   * @return array
   *   An array with blog post render arrays.
   */
  protected function getBlogPosts(TermInterface $term): array {
    $items = [];

    foreach ($this->loadBlogPosts($term) as $node) {
      $items[] = $this->nodeViewBuilder->view($node, 'teaser');
    }

    return $items;
  }

  /**
   * Loads blog entries.
   *
   * @param \Drupal\taxonomy\TermInterface $term
   *   The category term.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   The entities.
   */
  protected function loadBlogPosts(TermInterface $term): array {
    return $this->nodeStorage->loadMultiple($this->getBlogPostIds($term));
  }

  /**
   * Gets blog entry ids.
   *
   * @param \Drupal\taxonomy\TermInterface $term
   *   The category term.
   *
   * @return array|int
   *   The blog entry ids.
   */
  protected function getBlogPostIds(TermInterface $term): array|int {
    $query = $this->nodeStorage->getQuery()->accessCheck(FALSE);
    $query->condition('type', 'blog_entry')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('field_tags', $term->id())
      ->pager()
      ->sort('created', 'DESC');

    return $query->execute();
  }

}
