<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Finder;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Data\ExternalContentFileCollection;

/**
 * Represents an external content finder.
 */
interface ExternalContentFinderInterface extends EnvironmentAwareInterface {

  /**
   * Finds an external content files.
   */
  public function find(): ExternalContentFileCollection;

}
