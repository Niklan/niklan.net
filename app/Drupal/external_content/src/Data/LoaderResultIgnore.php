<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Provides an 'ignore' loader results.
 *
 * Use this result in cases when loader failed to load content or in cases when
 * loader can process a content, but doesn't want to do so and doesn't want
 * other loaders to try loading it.
 */
final class LoaderResultIgnore extends LoaderResult {

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
    return FALSE;
  }

}
