<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Node\ExternalContentDocument;

/**
 * Represents a collection of external content documents.
 */
final class ExternalContentDocumentCollection implements \Countable, \IteratorAggregate {

  /**
   * The array with parsed documents.
   *
   * @var \Drupal\external_content\Data\ExternalContentDocumentCollection[]
   */
  protected array $items = [];

  /**
   * Adds an external content document into collection.
   *
   * @param \Drupal\external_content\Node\ExternalContentDocument $document
   *   The document.
   */
  public function add(ExternalContentDocument $document): void {
    $this->items[] = $document;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \Traversable {
    return new \ArrayIterator($this->items);
  }

  /**
   * {@inheritdoc}
   */
  public function count(): int {
    return \count($this->items);
  }

}
