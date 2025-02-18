<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

/**
 * Provides a collection with loader results.
 *
 * @implements \IteratorAggregate<int, \Drupal\external_content\Data\LoaderResult>
 */
final class LoaderResultCollection implements \Countable, \IteratorAggregate {

  /**
   * The array with loader results.
   */
  protected array $items = [];

  /**
   * @return \ArrayIterator<int, \Drupal\external_content\Data\LoaderResult>
   */
  #[\Override]
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->items);
  }

  #[\Override]
  public function count(): int {
    return \count($this->items);
  }

  /**
   * Gets all successful result items.
   */
  public function getSuccessful(): self {
    $callback = static fn (LoaderResult $item) => $item->hasResults();

    return $this->filter($callback);
  }

  /**
   * Filters items from collection.
   */
  public function filter(callable $callback): self {
    $items = \array_filter($this->items, $callback);
    $result = new self();
    \array_walk(
      $items,
      static fn (LoaderResult $item) => $result->addResult($item),
    );

    return $result;
  }

  /**
   * Adds a result into collection.
   */
  public function addResult(LoaderResult $result): self {
    $this->items[] = $result;

    return $this;
  }

}
