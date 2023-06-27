<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Builder\BuilderResultInterface;

/**
 * Represents a builder result.
 */
abstract class BuilderResult implements BuilderResultInterface {

  /**
   * Builds a result without any output.
   */
  public static function none(): BuilderResultNone {
    return new BuilderResultNone();
  }

  /**
   * Builds a result with a render array.
   */
  public static function renderArray(array $render_array): BuilderResultRenderArray {
    return new BuilderResultRenderArray($render_array);
  }

}
