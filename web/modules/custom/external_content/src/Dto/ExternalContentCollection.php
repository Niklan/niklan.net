<?php

declare(strict_types=1);

namespace Drupal\external_content\Dto;

/**
 * Represents a collection of external content items.
 */
final class ExternalContentCollection implements \Countable, \IteratorAggregate {

  /**
   * The array with parsed source file items.
   *
   * @var \Drupal\external_content\Dto\ExternalContent[]
   */
  protected array $items = [];

  /**
   * Adds a parsed source file into collection.
   *
   * @param \Drupal\external_content\Dto\ExternalContent $file
   *   The external content.
   */
  public function add(ExternalContent $file): void {
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
