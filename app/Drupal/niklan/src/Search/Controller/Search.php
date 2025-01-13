<?php

declare(strict_types=1);

namespace Drupal\niklan\Search\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Search\Data\EntitySearchResult;
use Drupal\niklan\Search\Data\EntitySearchResults;
use Drupal\niklan\Search\Data\SearchParams;
use Drupal\niklan\Search\Repository\GlobalSearch;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class Search implements ContainerInjectionInterface {

  protected const int PER_PAGE = 10;

  public function __construct(
    protected GlobalSearch $entitySearch,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected PagerManagerInterface $pagerManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(GlobalSearch::class),
      $container->get(EntityTypeManagerInterface::class),
      $container->get(PagerManagerInterface::class),
    );
  }

  private function doSearch(string $keys): EntitySearchResults {
    $search_params = new SearchParams($keys, self::PER_PAGE, $this->pagerManager->findPage() * self::PER_PAGE);
    $search_results = $this->entitySearch->search($search_params);
    $this->pagerManager->createPager($search_results->getTotalResultsCount(), self::PER_PAGE);

    return $search_results;
  }

  private function buildResults(?string $query): array {
    if (!$query) {
      return [];
    }

    $search_results = $this->doSearch($query);
    if ($search_results->getTotalResultsCount() === 0) {
      return [];
    }

    $this->warmUpResults($search_results);

    return \array_map($this->buildSearchResult(...), $search_results->getItems());
  }

  private function buildSearchResult(EntitySearchResult $result): array {
    $storage = $this->entityTypeManager->getStorage($result->getEntityTypeId());
    $view_builder = $this->entityTypeManager->getViewBuilder($result->getEntityTypeId());
    $entity = $storage->load($result->getEntityId());

    return $view_builder->view($entity, 'search_result');
  }

  private function warmUpResults(EntitySearchResults $search_results): void {
    foreach ($search_results->getEntityIds() as $entity_type_id => $entity_ids) {
      $this->entityTypeManager->getStorage($entity_type_id)->loadMultiple($entity_ids);
    }
  }

  public function __invoke(Request $request): array {
    $query = $request->query->get('q');

    return [
      '#theme' => 'niklan_search_results',
      '#no_query' => new TranslatableMarkup('You need to provide a search query to see the results.'),
      '#no_results' => new TranslatableMarkup('Nothing was found for your request.'),
      '#results' => $this->buildResults($query),
      '#query' => $query,
      '#pager' => ['#type' => 'pager', '#quantity' => 5],
      '#cache' => [
        'contexts' => [
          'url.query_args:q',
          'url.query_args.pagers:0',
        ],
        'tags' => [
          'search_api_list:global_index',
        ],
      ],
    ];
  }

}
