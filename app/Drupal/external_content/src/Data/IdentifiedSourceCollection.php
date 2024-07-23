<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

final class IdentifiedSourceCollection {

  private array $sources = [];

  public function add(IdentifiedSource $source): void {
    $this->sources[] = $source;
  }

  public function merge(self $collection): self {
    $this->sources = \array_merge($this->sources, $collection->sources());

    return $this;
  }

  /**
   * @return \Drupal\external_content\Data\IdentifiedSource[]
   *   The identified sources.
   */
  public function sources(): array {
    return $this->sources;
  }

}
