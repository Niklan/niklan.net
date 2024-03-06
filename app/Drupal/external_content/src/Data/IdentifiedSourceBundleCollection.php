<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * {@selfdoc}
 */
final class IdentifiedSourceBundleCollection {

  /**
   * {@selfdoc}
   */
  private array $bundles = [];

  /**
   * {@selfdoc}
   */
  public function add(IdentifiedSourceBundle $bundle): void {
    $this->bundles[] = $bundle;
  }

  /**
   * {@selfdoc}
   */
  public function merge(self $collection): self {
    $this->bundles = \array_merge($this->bundles, $collection->bundles());

    return $this;
  }

  /**
   * {@selfdoc}
   *
   * @return \Drupal\external_content\Data\IdentifiedSourceBundle[]
   */
  public function bundles(): array {
    return $this->bundles;
  }

}
