<?php

declare(strict_types=1);

namespace Drupal\niklan\Search\Utils;

use Drupal\niklan\Search\Data\EntitySearchResult;
use Drupal\search_api\Query\ResultSetInterface;

final class SearchApiResultItemsHelper {

  /**
   * @return array<string, \Drupal\niklan\Search\Data\EntitySearchResult>
   */
  public static function extractEntityIds(ResultSetInterface $result_set): array {
    $entity_ids = [];

    foreach ($result_set->getResultItems() as $result_item) {
      if (!\preg_match('/entity:[a-z_]+\/[0-9]+:[a-z]{2}/m', $result_item->getId())) {
        continue;
      }

      [$entity_info, $source_info] = \explode('/', $result_item->getId());
      [, $entity_type_id] = \explode(':', $entity_info);
      [$entity_id, $language] = \explode(':', $source_info);

      $entity_ids[$result_item->getId()] = new EntitySearchResult($entity_type_id, $entity_id, $language);
    }

    return $entity_ids;
  }

}
