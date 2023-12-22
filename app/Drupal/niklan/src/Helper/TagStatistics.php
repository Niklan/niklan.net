<?php declare(strict_types = 1);

namespace Drupal\niklan\Helper;

use Drupal\Core\Database\Connection;

/**
 * Provides class for statistics of tags.
 */
final class TagStatistics implements TagStatisticsInterface {

  /**
   * Constructs a new TagStatistics object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(
    protected Connection $connection,
  ) {}

  /**
   * {@inheritdoc}
   */
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
