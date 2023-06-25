<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\Bundler\BundlerResultInterface;

/**
 * Represents a bundler result.
 */
abstract class BundlerResult implements BundlerResultInterface {

  /**
   * Builds a result for identified bundler result.
   *
   * @param string $id
   *   The document identifier.
   * @param \Drupal\external_content\Data\Attributes $attributes
   *   The document attributes.
   */
  public static function match(string $id, Attributes $attributes): BundlerResultIdentified {
    return new BundlerResultIdentified($id, $attributes);
  }

  /**
   * Builds a result for unidentified bundler result.
   */
  public static function unidentified(): BundlerResultUnidentified {
    return new BundlerResultUnidentified();
  }

}
