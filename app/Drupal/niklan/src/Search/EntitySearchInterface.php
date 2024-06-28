<?php

declare(strict_types=1);

namespace Drupal\niklan\Search;

use Drupal\niklan\Data\EntitySearchResults;
use Drupal\niklan\Data\SearchParams;

/**
 * Defines a common entity search interface.
 */
interface EntitySearchInterface {

  /**
   * Does search for provided search params.
   *
   * @param \Drupal\niklan\Data\SearchParams $params
   *   The search params.
   */
  public function search(SearchParams $params): EntitySearchResults;

}
