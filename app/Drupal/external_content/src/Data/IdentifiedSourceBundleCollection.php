<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * {@selfdoc}
 */
final readonly class IdentifiedSourceBundleCollection {

  /**
   * {@selfdoc}
   */
  private \SplObjectStorage $bundles;

  /**
   * {@selfdoc}
   */
  public function __construct() {
    $this->bundles = new \SplObjectStorage();
  }

  /**
   * {@selfdoc}
   */
  public function add(IdentifiedSourceBundle $bundle): void {
    if ($this->bundles->contains($bundle)) {
      return;
    }

    $this->bundles->attach($bundle);
  }

  /**
   * {@selfdoc}
   */
  public function bundles(): \SplObjectStorage {
    return $this->bundles;
  }

  /**
   * {@selfdoc}
   */
  public function merge(self $collection): self {
    $this->bundles->addAll($collection->bundles());

    return $this;
  }

}
