<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

/**
 * Provides a basic loader result implementation.
 */
final readonly class LoaderResult {

  public function __construct(
    private bool $shouldContinue,
    private string $bundleId,
    private array $results = [],
  ) {}

  /**
   * Returned when loader don't want or can't load the source.
   *
   * But also allows loader manager to try other loaders from environment.
   */
  public static function pass(string $bundle_id): self {
    return new self(TRUE, $bundle_id);
  }

  /**
   * Returned when loader loading process should be stopped with no result.
   */
  public static function stop(string $bundle_id): self {
    return new self(FALSE, $bundle_id);
  }

  public static function withResults(string $bundle_id, array $results): self {
    return new self(FALSE, $bundle_id, $results);
  }

  public function shouldNotContinue(): bool {
    return !$this->shouldContinue();
  }

  public function shouldContinue(): bool {
    return $this->shouldContinue;
  }

  public function results(): array {
    return $this->results;
  }

  public function hasNoResults(): bool {
    return !$this->hasResults();
  }

  public function hasResults(): bool {
    return \count($this->results) > 0;
  }

  public function bundleId(): string {
    return $this->bundleId;
  }

}
