<?php

declare(strict_types = 1);

namespace Drupal\external_content\Dto;

/**
 * Represents a collection of source files.
 */
final class SourceFileCollection implements \Countable, \IteratorAggregate {

  /**
   * The array with source file items.
   *
   * @var \Drupal\external_content\Dto\SourceFile[]
   */
  protected array $items = [];

  /**
   * Adds a source file into collection.
   *
   * @param \Drupal\external_content\Dto\SourceFile $file
   *   The source file.
   */
  public function add(SourceFile $file): void {
    $this->items[] = $file;
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
