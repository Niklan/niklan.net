<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Node\Content;

/**
 * Represents a collection of external content documents.
 */
final class ContentCollection implements \Countable, \IteratorAggregate {

  /**
   * The array with parsed documents.
   *
   * @var \Drupal\external_content\Data\ContentCollection[]
   */
  protected array $items = [];

  /**
   * Adds an external content document into collection.
   *
   * @param \Drupal\external_content\Node\Content $document
   *   The document.
   */
  public function add(Content $document): void {
    $this->items[] = $document;
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
