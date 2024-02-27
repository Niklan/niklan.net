<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Source\SourceInterface;

/**
 * {@selfdoc}
 */
final class SourceCollection {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private \SplObjectStorage $items = new \SplObjectStorage(),
  ) {}

  /**
   * {@selfdoc}
   */
  public function add(SourceInterface $source): void {
    if ($this->items->contains($source)) {
      return;
    }

    $this->items->attach($source);
  }

  /**
   * {@selfdoc}
   */
  public function items(): \SplObjectStorage {
    return $this->items;
  }

  /**
   * {@selfdoc}
   */
  public function merge(self $collection): self {
    $this->items->addAll($collection->items());

    return $this;
  }

}
