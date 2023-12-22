<?php declare(strict_types = 1);

namespace Drupal\niklan\Data;

/**
 * Provides a collection with entity search results.
 */
final class EntitySearchResults implements \IteratorAggregate, \Countable {

  /**
   * Constructs a new EntitySearchResults instance.
   *
   * @param \Drupal\niklan\Data\EntitySearchResult[] $items
   *   An array with search results.
   * @param int|null $totalResultsCount
   *   The total amount of results associated with search results. NULL if not
   *   applicable.
   */
  public function __construct(
    protected array $items,
    protected ?int $totalResultsCount = NULL,
  ) {}

  /**
   * Gets the search result items.
   *
   * @return \Drupal\niklan\Data\EntitySearchResult[]
   *   An array with search result items.
   */
  public function getItems(): array {
    return $this->items;
  }

  /**
   * Gets an array with unique entity type IDs used in results.
   *
   * @return array
   *   An array with entity type IDs.
   */
  public function getEntityTypeIds(): array {
    return \array_keys($this->getEntityIds());
  }

  /**
   * Gets an array with all entity IDs.
   *
   * @return array
   *   An array with entity IDs, keyed by entity type ID and array of IDs as its
   *   value.
   */
  public function getEntityIds(): array {
    $result = [];

    foreach ($this->items as $item) {
      $result[$item->getEntityTypeId()][] = $item->getEntityId();
    }

    return $result;
  }

  /**
   * Gets a total results count.
   */
  public function getTotalResultsCount(): ?int {
    return $this->totalResultsCount;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->items);
  }

  /**
   * {@inheritdoc}
   */
  public function count(): int {
    return \count($this->items);
  }

}
