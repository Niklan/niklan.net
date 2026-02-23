<?php

declare(strict_types=1);

namespace Drupal\app_search\Repository;

use Drupal\app_search\Data\EntitySearchResults;
use Drupal\app_search\Data\SearchParams;

interface EntitySearch {

  public function search(SearchParams $params): EntitySearchResults;

}
