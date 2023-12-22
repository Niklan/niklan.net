<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Provides a 'skip' loader result.
 *
 * Use this result if loader dimple doesn't want or can't load provided content
 * and loader should try to next use available loader.
 */
final class LoaderResultSkip extends LoaderResult {

  /**
   * {@inheritdoc}
   */
  public function isSuccess(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isNotSuccess(): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldContinue(): bool {
    return TRUE;
  }

}
