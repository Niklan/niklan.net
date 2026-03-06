<?php

declare(strict_types=1);

namespace Drupal\app_search\Data;

/**
 * @implements \IteratorAggregate<string, \Drupal\app_search\Data\EntitySearchResult>
 */
final readonly class EntitySearchResults implements \IteratorAggregate, \Countable {

  public function __construct(
    public array $items,
    public ?int $totalResultsCount = NULL,
  ) {}

  public function getEntityTypeIds(): array {
    return \array_keys($this->getEntityIds());
  }

  public function getEntityIds(): array {
    $result = [];

    foreach ($this->items as $item) {
      \assert($item instanceof EntitySearchResult);
      $result[$item->entityTypeId][] = $item->entityId;
    }

    return $result;
  }

  /**
   * @return \ArrayIterator<string, \Drupal\app_search\Data\EntitySearchResult>
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
