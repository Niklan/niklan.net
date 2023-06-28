<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Represents a collection of source files.
 */
final class ExternalContentFileCollection implements \Countable, \IteratorAggregate {

  /**
   * The array with source file items.
   *
   * @var \Drupal\external_content\Data\ExternalContentFile[]
   */
  protected array $items = [];

  /**
   * Adds a source file into collection.
   *
   * @param \Drupal\external_content\Data\ExternalContentFile $file
   *   The source file.
   */
  public function add(ExternalContentFile $file): void {
    $this->items[$file->getPathname()] = $file;
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
   * Merge provided collection into current one.
   *
   * @param \Drupal\external_content\Data\ExternalContentFileCollection $collection
   *   The collection to merge from.
   */
  public function merge(self $collection): self {
    foreach ($collection as $item) {
      \assert($item instanceof ExternalContentFile);
      $this->items[$item->getPathname()] = $item;
    }

    return $this;
  }

}
