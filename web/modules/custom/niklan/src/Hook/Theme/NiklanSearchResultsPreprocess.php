<?php

declare(strict_types = 1);

namespace Drupal\niklan\Hook\Theme;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\niklan\Data\ContentEntityResultSet;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides theme hook preprocess for search results.
 *
 * @see template_preprocess_niklan_search_results()
 */
final class NiklanSearchResultsPreprocess implements ContainerInjectionInterface {

  /**
   * Constructs a new NiklanSearchResultsPreprocess instance.
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
   * Implements hook_preprocess_HOOK().
   */
  public function __invoke(array &$variables): void {
    $results = $variables['results'];

    if ($results instanceof ContentEntityResultSet) {
      $variables['results'] = $this->buildResults($results);
    }

    $variables['pager'] = [
      '#type' => 'pager',
    ];
  }

  /**
   * Builds search results.
   *
   * @param \Drupal\niklan\Data\ContentEntityResultSet $results
   *   The search result set.
   *
   * @return array
   *   An array with search results.
   */
  private function buildResults(ContentEntityResultSet $results): array {
    $entity_type_id = $results->getEntityTypeId();
    $storage = $this->entityTypeManager->getStorage($entity_type_id);
    $view_builder = $this->entityTypeManager->getViewBuilder($entity_type_id);

    return \array_map(
      static fn (ContentEntityInterface $entity) => $view_builder->view(
        $entity,
        'search_result',
      ),
      $storage->loadMultiple($results->getIds()),
    );
  }

}
