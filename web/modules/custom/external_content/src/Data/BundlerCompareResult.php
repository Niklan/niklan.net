<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Bundler\BundlerCompareResultInterface;

/**
 * Represents a bundle compare result.
 */
abstract class BundlerCompareResult implements BundlerCompareResultInterface {

  /**
   * Builds a match result.
   *
   * @param string $reason
   *   The reason.
   * @param string $reason_id
   *   The reason ID.
   */
  public static function match(string $reason, string $reason_id): BundlerCompareResultMatch {
    return new BundlerCompareResultMatch($reason, $reason_id);
  }

  /**
   * Builds a pass result.
   */
  public static function pass(): BundlerCompareResultPass {
    return new BundlerCompareResultPass();
  }

}
