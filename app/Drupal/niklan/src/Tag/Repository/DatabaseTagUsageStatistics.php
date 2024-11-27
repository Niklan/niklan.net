<?php

declare(strict_types=1);

namespace Drupal\niklan\Tag\Repository;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;

final readonly class DatabaseTagUsageStatistics implements TagUsageStatistics {

  public function __construct(
    private Connection $connection,
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public function usage(?int $limit = NULL): array {
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

  #[\Override]
  public function count(int $tag_id): int {
    return $this
      ->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'blog_entry')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('field_tags', $tag_id)
      ->count()
      ->execute();
  }

  #[\Override]
  public function firstPublicationDate(int $tag_id): ?int {
    $query = $this
      ->connection
      ->select('node_field_data', 'nd')
      ->fields('nd', ['created']);
    $query->leftJoin('node__field_tags', 'nft', 'nd.nid = nft.entity_id');
    $query
      ->condition('nft.field_tags_target_id', $tag_id)
      ->orderBy('nid',)
      ->range(0, 1);
    $result = $query->execute()->fetch();

    if (!$result) {
      return NULL;
    }

    // @phpstan-ignore-next-line
    return (int) $result->created;
  }

  #[\Override]
  public function lastPublicationDate(int $tag_id): ?int {
    $query = $this
      ->connection
      ->select('node_field_data', 'nd')
      ->fields('nd', ['created']);
    $query->leftJoin('node__field_tags', 'nft', 'nd.nid = nft.entity_id');
    $query
      ->condition('nft.field_tags_target_id', $tag_id)
      ->orderBy('nid', 'DESC')
      ->range(0, 1);
    $result = $query->execute()->fetch();

    if (!$result) {
      return NULL;
    }

    // @phpstan-ignore-next-line
    return (int) $result->created;
  }

}
