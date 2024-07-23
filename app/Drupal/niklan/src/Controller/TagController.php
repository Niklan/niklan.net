<?php

declare(strict_types=1);

namespace Drupal\niklan\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\niklan\Helper\TagStatisticsInterface;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides controller for tags.
 */
final class TagController implements TagControllerInterface, ContainerInjectionInterface {

  /**
   * Constructs a new TagController instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\niklan\Helper\TagStatisticsInterface $tagStatistics
   *   The tag statistics.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected TagStatisticsInterface $tagStatistics,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity_type.manager'),
      $container->get('niklan.helper.tag_statistics'),
    );
  }

  #[\Override]
  public function collection(): array {
    $tag_ids = \array_keys($this->tagStatistics->getBlogEntryUsage());
    $terms = $this
      ->entityTypeManager
      ->getStorage('taxonomy_term')
      ->loadMultiple($tag_ids);
    $view_builder = $this->entityTypeManager->getViewBuilder('taxonomy_term');

    return [
      '#theme' => 'niklan_tag_list',
      '#items' => \array_map(
        static fn ($term) => $view_builder->view($term, 'teaser'),
        $terms,
      ),
    ];
  }

  #[\Override]
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
    $storage = $this->entityTypeManager->getStorage('node');
    $view_builder = $this->entityTypeManager->getViewBuilder('node');

    return \array_map(
      static fn (NodeInterface $node): array => $view_builder->view($node, 'teaser'),
      $storage->loadMultiple($this->getBlogPostIds($term)),
    );
  }

  /**
   * Gets blog entry ids.
   *
   * @param \Drupal\taxonomy\TermInterface $term
   *   The category term.
   */
  protected function getBlogPostIds(TermInterface $term): array|int {
    $query = $this
      ->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE);

    $query
      ->condition('type', 'blog_entry')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('field_tags', $term->id())
      ->pager()
      ->sort('created', 'DESC');

    return $query->execute();
  }

}
