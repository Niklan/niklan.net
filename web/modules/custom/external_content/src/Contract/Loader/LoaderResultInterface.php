<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Loader;

/**
 * Represents a result for external content loader.
 */
interface LoaderResultInterface {

  /**
   * Indicates whether loading was successful or not.
   */
  public function isSuccess(): bool;

  /**
   * Indicates whether loading was unsuccessful or not.
   */
  public function isNotSuccess(): bool;

  /**
   * Indicated that this document should be processed by other loaders if any.
   */
  public function shouldContinue(): bool;

}
