<?php declare(strict_types = 1);

namespace Drupal\niklan\Helper;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeStorageInterface;
use Drupal\taxonomy\TermStorageInterface;

/**
 * Provides class for statistics of tags.
 */
final class TagStatistics {

  /**
   * The term storage.
   */
  protected TermStorageInterface $termStorage;

  /**
   * The node storage.
   */
  protected NodeStorageInterface $nodeStorage;

  /**
   * The database connection.
   */
  protected Connection $connection;

  /**
   * Constructs a new TagStatistics object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Connection $connection) {
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->connection = $connection;
  }

  /**
   * Gets most used tags from blog entries.
   *
   * @param int|null $limit
   *   The maximum amount of entries to fetch.
   *
   * @return \stdClass[]
   *   An array with term results.
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
