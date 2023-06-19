<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContentFileCollection;

/**
 * Represents an external content finder.
 */
interface ExternalContentFinderInterface extends EnvironmentAwareInterface {

  /**
   * Finds an external content files.
   *
   * @return \Drupal\external_content\Data\ExternalContentFileCollection
   *   The list external content files.
   */
  public function find(): ExternalContentFileCollection;

}
