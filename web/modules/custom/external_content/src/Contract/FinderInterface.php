<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContentFileCollection;

/**
 * Represents a specific external content finder.
 */
interface FinderInterface {

  /**
   * Finds an external content files.
   *
   * @param \Drupal\external_content\Contract\EnvironmentInterface $environment
   *   The environment.
   *
   * @return \Drupal\external_content\Data\ExternalContentFileCollection
   *   The external content files.
   */
  public function find(EnvironmentInterface $environment): ExternalContentFileCollection;

}
