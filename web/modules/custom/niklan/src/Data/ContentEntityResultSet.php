<?php declare(strict_types = 1);

namespace Drupal\niklan\Data;

/**
 * Provides class to store product search results.
 */
final class ContentEntityResultSet implements \IteratorAggregate, \Countable {

  /**
   * ProductSearchResultSet constructor.
   *
   * @param string $entityTypeId
   *   The entity type ID for result set.
   * @param array $entityIds
   *   The entity IDs.
   * @param int $resultCount
   *   The total amount of results associated with this result set.
   */
  public function __construct(protected string $entityTypeId, protected array $entityIds, protected int $resultCount,) {}

  /**
   * Gets entity type ID.
   *
   * @return string
   *   The entity type ID.
   */
  public function getEntityTypeId(): string {
    return $this->entityTypeId;
  }

  /**
   * Gets result product IDs.
   *
   * @return array
   *   An array with product IDs.
   */
  public function getIds(): array {
    return $this->entityIds;
  }

  /**
   * Gets total results.
   *
   * @return int
   *   The amount of results for search.
   */
  public function getResultCount(): int {
    return $this->resultCount;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->entityIds);
  }

  /**
   * {@inheritdoc}
   */
  public function count(): int {
    return \count($this->entityIds);
  }

}
