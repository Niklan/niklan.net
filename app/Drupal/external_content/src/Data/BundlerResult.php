<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * {@selfdoc}
 */
final readonly class BundlerResult {

  /**
   * {@selfdoc}
   */
  public function __construct(
    public ?string $bundleId,
  ) {}

  /**
   * {@selfdoc}
   */
  public function shouldBeBundled(): bool {
    return \is_null($this->bundleId) || \strlen($this->bundleId) > 0;
  }

  /**
   * {@selfdoc}
   */
  public function shouldNotBeBundled(): bool {
    return !$this->shouldBeBundled();
  }

  /**
   * {@selfdoc}
   */
  public static function bundleAs(string $bundle_id): self {
    return new self($bundle_id);
  }

  /**
   * {@selfdoc}
   */
  public static function pass(): self {
    return new self(NULL);
  }

}
