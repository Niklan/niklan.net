<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Represents a collection of external content bundles.
 */
final class SourceBundleCollection implements \Countable, \IteratorAggregate {

  /**
   * {@selfdoc}
   */
  protected array $items = [];

  /**
   * {@selfdoc}
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
