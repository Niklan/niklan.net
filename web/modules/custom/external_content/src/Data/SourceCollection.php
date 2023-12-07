<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Source\SourceInterface;

/**
 * {@selfdoc}
 */
final class SourceCollection implements \Countable, \IteratorAggregate {

  /**
   * {@selfdoc}
   */
  protected array $items = [];

  /**
   * {@selfdoc}
   */
  public function add(SourceInterface $source): void {
    $this->items[$source->id()] = $source;
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
   * {@selfdoc}
   */
  public function merge(self $collection): self {
    foreach ($collection as $source) {
      \assert($source instanceof SourceInterface);
      $this->items[$source->id()] = $source;
    }

    return $this;
  }

}
