<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Provides a collection with loader results.
 */
final class LoaderResultCollection implements \Countable, \IteratorAggregate {

  /**
   * The array with loader results.
   */
  protected array $items = [];

  /**
   * Adds a result into collection.
   */
  public function addResult(LoaderResult $result): self {
    $this->items[] = $result;

    return $this;
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
   * Gets all successful result items.
   */
  public function getSuccessful(): self {
    $callback = static fn (LoaderResult $item) => $item->hasResults();

    return $this->filter($callback);
  }

}
