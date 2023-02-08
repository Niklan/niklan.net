<?php

declare(strict_types=1);

namespace Drupal\niklan\Utility;

use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Query\ResultSetInterface;

/**
 * Provides utility functions for search api results.
 */
final class SearchApiResultItemsHelper {

  /**
   * Extract entity ID's from result set.
   *
   * @param \Drupal\search_api\Query\ResultSetInterface $result_set
   *   The result set.
   *
   * @return array
   *   An array with entity IDs.
   */
  public static function extractEntityIds(ResultSetInterface $result_set): array {
    return \array_map(static function (ItemInterface $result_item) {
      [, $source_info] = \explode('/', $result_item->getId());
      [$source_id] = \explode(':', $source_info);
      return $source_id;
    }, $result_set->getResultItems());
  }

}
