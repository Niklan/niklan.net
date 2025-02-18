<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

/**
 * Provides a list that takes priority into account.
 *
 * @template T
 * @implements \IteratorAggregate<int, array<int, T>>
 */
final class PrioritizedList implements \IteratorAggregate {

  /**
   * The array with items, grouped by priority.
   *
   * @var array<int, array<int, T>>
   */
  protected array $list = [];

  /**
   * The sorted list of items.
   *
   * @var \ArrayIterator<int, T>|null
   */
  protected ?\ArrayIterator $sorted = NULL;

  /**
   * Adds an item into a list.
   *
   * @param T $item
   *   The value to add.
   * @param int $priority
   *   The item priority in the list.
   */
  public function add(mixed $item, int $priority): void {
    $this->list[$priority][] = $item;
    $this->sorted = NULL;
  }

  /**
   * @return \ArrayIterator<int, T>
   */
  #[\Override]
  public function getIterator(): \ArrayIterator {
    if ($this->sorted) {
      return $this->sorted;
    }

    \krsort($this->list);
    $sorted = [];

    foreach ($this->list as $priority_list) {
      foreach ($priority_list as $item) {
        $sorted[] = $item;
      }
    }

    $this->sorted = new \ArrayIterator($sorted);

    return $this->sorted;
  }

}
