<?php

declare(strict_types=1);

namespace Drupal\niklan\Search\Data;

/**
 * @implements \IteratorAggregate<string, \Drupal\niklan\Search\Data\EntitySearchResult>
 */
final class EntitySearchResults implements \IteratorAggregate, \Countable {

  public function __construct(
    protected array $items,
    protected ?int $totalResultsCount = NULL,
  ) {}

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

  /**
   * @return \ArrayIterator<string, \Drupal\niklan\Search\Data\EntitySearchResult>
   */
  #[\Override]
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->items);
  }

  #[\Override]
  public function count(): int {
    return \count($this->items);
  }

}
