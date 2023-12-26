<?php declare(strict_types = 1);

namespace Drupal\niklan\Helper;

use Drupal\niklan\Data\EntitySearchResult;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Query\ResultSetInterface;

/**
 * Provides utility functions for search api results.
 */
final class SearchApiResultItemsHelper {

  /**
   * Extract entity ID's from result set.
   *
   * @param \Drupal\search_api\Query\ResultSetInterface<\Drupal\search_api\Item\ItemInterface> $result_set
   *   The result set.
   *
   * @return \Drupal\niklan\Data\EntitySearchResult[]
   *   An array with entity IDs.
   */
  public static function extractEntityIds(ResultSetInterface $result_set): array {
    return \array_map(static function (ItemInterface $result_item) {
      [$entity_info, $source_info] = \explode('/', $result_item->getId());
      [, $entity_type_id] = \explode(':', $entity_info);
      [$entity_id, $language] = \explode(':', $source_info);

      return new EntitySearchResult($entity_type_id, $entity_id, $language);
    }, $result_set->getResultItems());
  }

}
