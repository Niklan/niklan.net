<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Bundler\BundlerCompareResultReasonInterface;

/**
 * Provides a value object for bundler compare matched result.
 */
final class BundlerCompareResultMatch extends BundlerCompareResult implements BundlerCompareResultReasonInterface {

  /**
   * Constructs a new BundlerCompareResultMatch instance.
   *
   * @param string $reason
   *   The reason.
   * @param string $reasonId
   *   The reason ID.
   */
  public function __construct(
    protected string $reason,
    protected string $reasonId,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function isMatch(): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function isNotMatch(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getReason(): string {
    return $this->reason;
  }

  /**
   * {@inheritdoc}
   */
  public function getReasonId(): string {
    return $this->reasonId;
  }

}
