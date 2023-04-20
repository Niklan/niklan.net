<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Represents a collection of parsed source files.
 */
final class ParsedSourceFileCollection implements \Countable, \IteratorAggregate {

  /**
   * The array with parsed source file items.
   *
   * @var \Drupal\external_content\Data\ParsedSourceFile[]
   */
  protected array $items = [];

  /**
   * Adds a parsed source file into collection.
   *
   * @param \Drupal\external_content\Data\ParsedSourceFile $file
   *   The source file.
   */
  public function add(ParsedSourceFile $file): void {
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
