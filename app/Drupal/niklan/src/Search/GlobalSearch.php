<?php

declare(strict_types=1);

namespace Drupal\niklan\Search;

use Drupal\niklan\Data\EntitySearchResults;
use Drupal\niklan\Data\SearchParams;
use Drupal\niklan\Helper\SearchApiResultItemsHelper;

/**
 * Provides a global site search.
 */
final class GlobalSearch extends SearchApiSearch implements EntitySearchInterface {

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
