<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Source\SourceInterface;

/**
 * {@selfdoc}
 */
final class SourceCollection {

  /**
   * {@selfdoc}
   */
  private array $items = [];

  /**
   * {@selfdoc}
   */
  public function add(SourceInterface $source): void {
    $this->items[] = $source;
  }

  /**
   * {@selfdoc}
   */
  public function merge(self $collection): self {
    $this->items = \array_merge($this->items, $collection->items());

    return $this;
  }

  /**
   * {@selfdoc}
   *
   * @return \Drupal\external_content\Contract\Source\SourceInterface[]
   *   The sources.
   */
  public function items(): array {
    return $this->items;
  }

}
