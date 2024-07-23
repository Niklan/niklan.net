<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

final class IdentifiedSourceBundleCollection {

  private array $bundles = [];

  public function add(IdentifiedSourceBundle $bundle): void {
    $this->bundles[] = $bundle;
  }

  public function merge(self $collection): self {
    $this->bundles = \array_merge($this->bundles, $collection->bundles());

    return $this;
  }

  /**
   * @return \Drupal\external_content\Data\IdentifiedSourceBundle[]
   *   The identified bundles.
   */
  public function bundles(): array {
    return $this->bundles;
  }

}
