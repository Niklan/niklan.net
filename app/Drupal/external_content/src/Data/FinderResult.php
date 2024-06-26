<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * {@selfdoc}
 */
final readonly class FinderResult {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private ?SourceCollection $result = NULL,
  ) {}

  /**
   * {@selfdoc}
   */
  public static function withSources(SourceCollection $collection): self {
    return new self($collection);
  }

  /**
   * {@selfdoc}
   */
  public static function notFound(): self {
    return new self();
  }

  /**
   * {@selfdoc}
   */
  public function hasResults(): bool {
    return !$this->hasNoResults();
  }

  /**
   * {@selfdoc}
   */
  public function hasNoResults(): bool {
    return \is_null($this->result) || \count($this->result->items()) === 0;
  }

  /**
   * {@selfdoc}
   */
  public function results(): ?SourceCollection {
    return $this->result;
  }

}
