<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Loader;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Data\ExternalContentBundle;
use Drupal\external_content\Data\LoaderResultCollection;

/**
 * Represents an external content loader.
 */
interface ExternalContentLoaderInterface extends EnvironmentAwareInterface {

  /**
   * Loads a single content bundle.
   */
  public function load(ExternalContentBundle $bundle): LoaderResultCollection;

}
