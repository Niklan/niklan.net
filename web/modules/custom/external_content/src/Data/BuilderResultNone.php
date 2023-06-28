<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Represent a builder result that doesn't provide any result.
 */
final class BuilderResultNone extends BuilderResult {

  /**
   * {@inheritdoc}
   */
  public function isBuilt(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isNotBuild(): bool {
    return TRUE;
  }

}
