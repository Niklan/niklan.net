<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Builder;

/**
 * Represents a builder result with built render array.
 */
interface BuilderResultRenderArrayInterface extends BuilderResultInterface {

  /**
   * Gets built render array.
   */
  public function getRenderArray(): array;

}
