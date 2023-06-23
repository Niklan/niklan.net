<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Bundler;

/**
 * Represents a bundler compare result.
 */
interface BundlerCompareResultInterface {

  /**
   * Indicates whether the compared documents should be merged.
   */
  public function isMatch(): bool;

  /**
   * Indicates whether the compared documents shouldn't be merged.
   */
  public function isNotMatch(): bool;

}
