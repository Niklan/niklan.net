<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Bundler;

/**
 * Represents a bundler compare result.
 */
interface BundlerResultInterface {

  /**
   * Indicates whether the compared documents should be merged.
   */
  public function isIdentified(): bool;

  /**
   * Indicates whether the compared documents shouldn't be merged.
   */
  public function isUnidentified(): bool;

}
