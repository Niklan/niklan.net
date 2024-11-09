<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Theme;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\niklan\Search\Data\EntitySearchResult;
use Drupal\niklan\Search\Data\EntitySearchResults;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class NiklanSearchResultsPreprocess implements ContainerInjectionInterface {

  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity_type.manager'),
    );
  }

  protected function buildResults(EntitySearchResults $results): array {
    $this->warmUpResults($results);

    return \array_map([$this, 'buildResult'], $results->getItems());
  }

  protected function warmUpResults(EntitySearchResults $results): void {
    foreach ($results->getEntityIds() as $entity_type_id => $entity_ids) {
      $this
        ->entityTypeManager
        ->getStorage($entity_type_id)
        ->loadMultiple($entity_ids);
    }
  }

  protected function buildResult(EntitySearchResult $result): array {
    $storage = $this->entityTypeManager->getStorage($result->getEntityTypeId());
    $view_builder = $this->entityTypeManager->getViewBuilder(
      $result->getEntityTypeId(),
    );
    $entity = $storage->load($result->getEntityId());

    return $view_builder->view($entity, 'search_result');
  }

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

}
