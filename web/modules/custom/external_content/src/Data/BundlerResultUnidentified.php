<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Provides a value object for unidentified bundler result.
 */
final class BundlerResultUnidentified extends BundlerResult {

  /**
   * {@inheritdoc}
   */
  public function isIdentified(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isUnidentified(): bool {
    return TRUE;
  }

}
