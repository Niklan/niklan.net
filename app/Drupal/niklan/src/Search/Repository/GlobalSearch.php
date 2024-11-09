<?php

declare(strict_types=1);

namespace Drupal\niklan\Search\Repository;

use Drupal\niklan\Helper\SearchApiResultItemsHelper;
use Drupal\niklan\Search\Data\EntitySearchResults;
use Drupal\niklan\Search\Data\SearchParams;

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
