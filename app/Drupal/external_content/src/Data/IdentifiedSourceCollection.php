<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * {@selfdoc}
 */
final readonly class IdentifiedSourceCollection {

  /**
   * {@selfdoc}
   */
  private \SplObjectStorage $sources;

  /**
   * {@selfdoc}
   */
  public function __construct() {
    $this->sources = new \SplObjectStorage();
  }

  /**
   * {@selfdoc}
   */
  public function add(IdentifiedSource $source): void {
    if ($this->sources->contains($source)) {
      return;
    }

    $this->sources->attach($source);
  }

  /**
   * {@selfdoc}
   */
  public function sources(): \SplObjectStorage {
    return $this->sources;
  }

  /**
   * {@selfdoc}
   */
  public function merge(self $collection): self {
    $this->sources->addAll($collection->sources());

    return $this;
  }

}
