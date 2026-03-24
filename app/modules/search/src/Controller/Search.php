<?php

declare(strict_types=1);

namespace Drupal\app_search\Controller;

use Drupal\app_search\Data\EntitySearchResult;
use Drupal\app_search\Data\EntitySearchResults;
use Drupal\app_search\Data\SearchParams;
use Drupal\app_search\Repository\EntitySearch;
use Drupal\app_search\Repository\GlobalSearch;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;

final class Search {

  protected const int PER_PAGE = 10;

  public function __construct(
    #[Autowire(service: GlobalSearch::class)]
    protected EntitySearch $entitySearch,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected PagerManagerInterface $pagerManager,
    private TranslationInterface $stringTranslation,
  ) {}

  public function __invoke(Request $request): array {
    $query = (string) $request->query->get('q');

    return [
      '#theme' => 'app_search_results',
      '#no_query' => $this->stringTranslation->translate('You need to provide a search query to see the results.'),
      '#no_results' => $this->stringTranslation->translate('Nothing was found for your request.'),
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

  private function doSearch(string $keys): EntitySearchResults {
    $search_params = new SearchParams($keys, self::PER_PAGE, $this->pagerManager->findPage() * self::PER_PAGE);
    $search_results = $this->entitySearch->search($search_params);
    $this->pagerManager->createPager($search_results->totalResultsCount ?? 0, self::PER_PAGE);

    return $search_results;
  }

  private function buildResults(?string $query): array {
    if (!$query) {
      return [];
    }

    $search_results = $this->doSearch($query);
    if ($search_results->totalResultsCount === 0) {
      return [];
    }

    $this->warmUpResults($search_results);

    return \array_map($this->buildSearchResult(...), $search_results->items);
  }

  private function buildSearchResult(EntitySearchResult $result): array {
    $storage = $this->entityTypeManager->getStorage($result->entityTypeId);
    $view_builder = $this->entityTypeManager->getViewBuilder($result->entityTypeId);
    $entity = $storage->load($result->entityId);

    return $entity ? $view_builder->view($entity, 'search_result') : [];
  }

  private function warmUpResults(EntitySearchResults $search_results): void {
    foreach ($search_results->getEntityIds() as $entity_type_id => $entity_ids) {
      $this->entityTypeManager->getStorage($entity_type_id)->loadMultiple($entity_ids);
    }
  }

}
