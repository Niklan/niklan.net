<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

final readonly class BundlerResult {

  public function __construct(
    public ?string $bundleId,
  ) {}

  public static function bundleAs(string $bundle_id): self {
    return new self($bundle_id);
  }

  public static function pass(): self {
    return new self(NULL);
  }

  public function shouldNotBeBundled(): bool {
    return !$this->shouldBeBundled();
  }

  public function shouldBeBundled(): bool {
    return !\is_null($this->bundleId) && \strlen($this->bundleId) > 0;
  }

}
