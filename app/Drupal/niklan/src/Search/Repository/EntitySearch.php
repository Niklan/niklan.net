<?php

declare(strict_types=1);

namespace Drupal\niklan\Search\Repository;

use Drupal\niklan\Search\Data\EntitySearchResults;
use Drupal\niklan\Search\Data\SearchParams;

/**
 * @todo Consider remove it.
 */
interface EntitySearch {

  public function search(SearchParams $params): EntitySearchResults;

}
