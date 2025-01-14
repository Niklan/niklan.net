<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Node\Content;

/**
 * Represents a collection of external content documents.
 *
 * @implements \IteratorAggregate<int, \Drupal\external_content\Node\Content>
 */
final class ContentCollection implements \Countable, \IteratorAggregate {

  /**
   * The array with parsed documents.
   *
   * @var list<\Drupal\external_content\Node\Content>
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
   * @return \ArrayIterator<int, \Drupal\external_content\Node\Content>
   */
  #[\Override]
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->items);
  }

  #[\Override]
  public function count(): int {
    return \count($this->items);
  }

}
