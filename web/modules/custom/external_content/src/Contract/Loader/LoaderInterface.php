<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Loader;

use Drupal\external_content\Data\SourceVariant;

/**
 * Represents an external content loader.
 */
interface LoaderInterface {

  /**
   * Loads a single external content.
   */
  public function load(SourceVariant $bundle): LoaderResultInterface;

}
