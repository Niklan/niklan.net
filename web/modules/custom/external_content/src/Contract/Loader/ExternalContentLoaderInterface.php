<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Loader;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Data\ExternalContentBundle;

/**
 * Represents an external content loader.
 */
interface ExternalContentLoaderInterface extends EnvironmentAwareInterface {

  /**
   * Loads a single content bundle.
   *
   * @todo Think about return type. It should return all loader results for a
   *   bundle.
   */
  public function load(ExternalContentBundle $bundle): void;

}
