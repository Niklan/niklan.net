<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

/**
 * {@selfdoc}
 */
final class IdentifiedSourceCollection {

  /**
   * {@selfdoc}
   */
  private array $sources = [];

  /**
   * {@selfdoc}
   */
  public function add(IdentifiedSource $source): void {
    $this->sources[] = $source;
  }

  /**
   * {@selfdoc}
   */
  public function merge(self $collection): self {
    $this->sources = \array_merge($this->sources, $collection->sources());

    return $this;
  }

  /**
   * {@selfdoc}
   *
   * @return \Drupal\external_content\Data\IdentifiedSource[]
   *   The identified sources.
   */
  public function sources(): array {
    return $this->sources;
  }

}
