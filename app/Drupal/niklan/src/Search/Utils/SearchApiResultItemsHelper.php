<?php

declare(strict_types=1);

namespace Drupal\niklan\Search\Utils;

use Drupal\niklan\Search\Data\EntitySearchResult;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Query\ResultSetInterface;

final class SearchApiResultItemsHelper {

  public static function extractEntityIds(ResultSetInterface $result_set): array {
    return \array_map(static function (ItemInterface $result_item) {
      [$entity_info, $source_info] = \explode('/', $result_item->getId());
      [, $entity_type_id] = \explode(':', $entity_info);
      [$entity_id, $language] = \explode(':', $source_info);

      return new EntitySearchResult($entity_type_id, $entity_id, $language);
    }, $result_set->getResultItems());
  }

}
