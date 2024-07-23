<?php

declare(strict_types=1);

namespace Drupal\niklan\Search;

use Drupal\niklan\Data\EntitySearchResults;
use Drupal\niklan\Data\SearchParams;

interface EntitySearchInterface {

  public function search(SearchParams $params): EntitySearchResults;

}
