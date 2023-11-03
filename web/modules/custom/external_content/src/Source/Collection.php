<?php declare(strict_types = 1);

namespace Drupal\external_content\Source;

use Drupal\external_content\Contract\Source\SourceInterface;

/**
 * Represents a collection of source files.
 */
final class Collection implements \Countable, \IteratorAggregate {

  /**
   * {@selfdoc}
   *
   * @var \Drupal\external_content\Contract\Source\SourceInterface[]
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
    foreach ($collection as $item) {
      \assert($item instanceof File);
      $this->items[$item->getPathname()] = $item;
    }

    return $this;
  }

}
