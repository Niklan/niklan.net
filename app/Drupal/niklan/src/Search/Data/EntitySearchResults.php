<?php

declare(strict_types=1);

namespace Drupal\niklan\Search\Data;

final class EntitySearchResults implements \IteratorAggregate, \Countable {

  /**
   * @param \Drupal\niklan\Search\Data\EntitySearchResult[] $items
   *   An array with search results.
   */
  public function __construct(
    protected array $items,
    protected ?int $totalResultsCount = NULL,
  ) {}

  /**
   * @return \Drupal\niklan\Search\Data\EntitySearchResult[]
   *   An array with search result items.
   */
  public function getItems(): array {
    return $this->items;
  }

  public function getEntityTypeIds(): array {
    return \array_keys($this->getEntityIds());
  }

  public function getEntityIds(): array {
    $result = [];

    foreach ($this->items as $item) {
      $result[$item->getEntityTypeId()][] = $item->getEntityId();
    }

    return $result;
  }

  public function getTotalResultsCount(): ?int {
    return $this->totalResultsCount;
  }

  #[\Override]
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->items);
  }

  #[\Override]
  public function count(): int {
    return \count($this->items);
  }

}
