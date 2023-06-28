<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Builder;

/**
 * Represents a builder result.
 */
interface BuilderResultInterface {

  /**
   * Indicates whether result was built or not.
   */
  public function isBuilt(): bool;

  /**
   * Indicates whether result was not built or not.
   */
  public function isNotBuild(): bool;

}
