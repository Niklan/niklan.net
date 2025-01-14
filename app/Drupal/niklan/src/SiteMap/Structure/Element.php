<?php

declare(strict_types=1);

namespace Drupal\niklan\SiteMap\Structure;

/**
 * @implements \IteratorAggregate<mixed, mixed>
 * @implements \ArrayAccess<mixed, mixed>
 */
abstract class Element implements \IteratorAggregate, \Countable, \ArrayAccess {

  protected array $collection = [];

  abstract public function toArray(): array;

  #[\Override]
  public function getIterator(): \Traversable {
    return new \ArrayIterator($this->collection);
  }

  #[\Override]
  public function offsetExists(mixed $offset): bool {
    return isset($this->collection[$offset]);
  }

  #[\Override]
  public function offsetGet(mixed $offset): mixed {
    if (!isset($this->collection[$offset])) {
      throw new \OutOfBoundsException(\sprintf('The offset "%s" does not exist.', $offset));
    }

    return $this->collection[$offset];
  }

  #[\Override]
  public function offsetSet(mixed $offset, mixed $value): void {
    $this->collection[$offset] = $value;
  }

  #[\Override]
  public function offsetUnset(mixed $offset): void {
    unset($this->collection[$offset]);
  }

  #[\Override]
  public function count(): int {
    return \count($this->collection);
  }

}
