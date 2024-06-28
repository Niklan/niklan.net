<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Theme;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\niklan\Data\EntitySearchResult;
use Drupal\niklan\Data\EntitySearchResults;
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

    if (!$results instanceof EntitySearchResults) {
      return;
    }

    $variables['results'] = $this->buildResults($results);
    $variables['pager'] = [
      '#type' => 'pager',
    ];
  }

  /**
   * Builds search results.
   *
   * @param \Drupal\niklan\Data\EntitySearchResults $results
   *   The search result set.
   *
   * @return array
   *   An array with search results.
   */
  protected function buildResults(EntitySearchResults $results): array {
    $this->warmUpResults($results);

    return \array_map([$this, 'buildResult'], $results->getItems());
  }

  /**
   * Warms up result entities.
   *
   * @param \Drupal\niklan\Data\EntitySearchResults $results
   *   The search results.
   */
  protected function warmUpResults(EntitySearchResults $results): void {
    foreach ($results->getEntityIds() as $entity_type_id => $entity_ids) {
      $this
        ->entityTypeManager
        ->getStorage($entity_type_id)
        ->loadMultiple($entity_ids);
    }
  }

  /**
   * Builds a single result.
   *
   * @param \Drupal\niklan\Data\EntitySearchResult $result
   *   The entity search result.
   *
   * @return array
   *   A render array with result.
   */
  protected function buildResult(EntitySearchResult $result): array {
    $storage = $this->entityTypeManager->getStorage($result->getEntityTypeId());
    $view_builder = $this->entityTypeManager->getViewBuilder(
      $result->getEntityTypeId(),
    );
    $entity = $storage->load($result->getEntityId());

    return $view_builder->view($entity, 'search_result');
  }

}
