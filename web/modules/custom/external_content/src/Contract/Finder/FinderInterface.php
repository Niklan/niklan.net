<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Finder;

use Drupal\external_content\Data\ExternalContentFileCollection;

/**
 * Represents a specific external content finder.
 */
interface FinderInterface {

  /**
   * Finds an external content files.
   *
   * @return \Drupal\external_content\Data\ExternalContentFileCollection
   *   The external content files.
   */
  public function find(): ExternalContentFileCollection;

}
