<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

final readonly class FinderResult {

  public function __construct(
    private ?SourceCollection $result = NULL,
  ) {}

  public static function withSources(SourceCollection $collection): self {
    return new self($collection);
  }

  public static function notFound(): self {
    return new self();
  }

  public function hasResults(): bool {
    return !$this->hasNoResults();
  }

  /**
   * @phpstan-assert-if-true null $this->results()
   */
  public function hasNoResults(): bool {
    return \is_null($this->result) || \count($this->result->items()) === 0;
  }

  public function results(): ?SourceCollection {
    return $this->result;
  }

}
