<?php

declare(strict_types=1);

namespace Drupal\app_search\Repository;

use Drupal\app_search\Data\EntitySearchResults;
use Drupal\app_search\Data\SearchParams;
use Drupal\app_search\Utils\SearchApiResultItemsHelper;

final class GlobalSearch extends SearchApiSearch implements EntitySearch {

  #[\Override]
  public function search(SearchParams $params): EntitySearchResults {
    $query = $this->getQuery();
    $query->keys($params->getKeys());
    $query->range($params->getOffset(), $params->getLimit());
    $query->sort('search_api_relevance', 'DESC');

    $results = $query->execute();

    return new EntitySearchResults(
      SearchApiResultItemsHelper::extractEntityIds($results),
      (int) $results->getResultCount(),
    );
  }

  #[\Override]
  protected function getIndexId(): string {
    return 'global_index';
  }

}
