<?php

declare(strict_types=1);

namespace Drupal\niklan\Helper;

use Drupal\Core\Database\Connection;

final class TagStatistics implements TagStatisticsInterface {

  public function __construct(
    protected Connection $connection,
  ) {}

  #[\Override]
  public function getBlogEntryUsage(?int $limit = NULL): array {
    $query = $this->connection->select('taxonomy_term_field_data', 'terms');
    $query->leftJoin(
      'node__field_tags',
      'node_tags',
      'node_tags.field_tags_target_id = terms.tid',
    );
    $query->leftJoin(
      'node_field_data',
      'node_data',
      'node_data.nid = node_tags.entity_id',
    );
    $query->condition('terms.vid', 'tags');
    $query->condition('terms.status', '1');
    $query->addExpression('COUNT(node_data.nid)', 'count');
    $query->groupBy('terms.tid');
    $query->orderBy('count', 'DESC');
    $query->fields('terms', ['tid']);

    if ($limit) {
      $query->range(0, $limit);
    }

    return $query->execute()->fetchAllAssoc('tid');
  }

}
