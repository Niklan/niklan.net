<?php

declare(strict_types=1);

namespace Drupal\app_contract\Contract\SiteMap;

/**
 * @template T
 * @implements \IteratorAggregate<T>
 * @implements \ArrayAccess<int|string, T>
 */
abstract class Element implements \IteratorAggregate, \Countable, \ArrayAccess {

  /**
   * @var array<int|string, T>
   */
  protected array $collection = [];

  abstract public function toArray(): array;

  #[\Override]
  public function getIterator(): \Traversable {
    return new \ArrayIterator($this->collection);
  }

  /**
   * @param int|string $offset
   */
  #[\Override]
  public function offsetExists(mixed $offset): bool {
    return isset($this->collection[$offset]);
  }

  /**
   * @param int|string $offset
   */
  #[\Override]
  public function offsetGet(mixed $offset): mixed {
    if (!isset($this->collection[$offset])) {
      throw new \OutOfBoundsException(\sprintf('The offset "%s" does not exist.', $offset));
    }

    return $this->collection[$offset];
  }

  /**
   * @param int|string $offset
   */
  #[\Override]
  public function offsetSet(mixed $offset, mixed $value): void {
    $this->collection[$offset] = $value;
  }

  /**
   * @param int|string $offset
   */
  #[\Override]
  public function offsetUnset(mixed $offset): void {
    unset($this->collection[$offset]);
  }

  #[\Override]
  public function count(): int {
    return \count($this->collection);
  }

}
