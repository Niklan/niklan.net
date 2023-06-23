<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Provides a value object for bundler compare unmatched result.
 */
final class BundlerCompareResultPass extends BundlerCompareResult {

  /**
   * {@inheritdoc}
   */
  public function isMatch(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isNotMatch(): bool {
    return TRUE;
  }

}
