<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Represents a collection of external content bundles.
 */
final class ExternalContentBundleCollection implements \Countable, \IteratorAggregate {

  /**
   * The array with external content bundle items.
   */
  protected array $items = [];

  /**
   * Adds an external content bundle into collection.
   *
   * @param \Drupal\external_content\Data\SourceBundle $bundle
   *   The external content bundle.
   */
  public function add(SourceBundle $bundle): self {
    $this->items[] = $bundle;

    return $this;
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
