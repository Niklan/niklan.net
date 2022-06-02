<?php

declare(strict_types=1);

namespace Drupal\external_content\Finder;

use Drupal\external_content\Dto\SourceFileCollection;

/**
 * Provides a finder for content source files.
 */
interface SourceFileFinderInterface {

  /**
   * Searches for source files.
   *
   * @param string $working_dir
   *   The working dir.
   *
   * @return \Drupal\external_content\Dto\SourceFileCollection
   *   The found file collection.
   */
  public function find(string $working_dir): SourceFileCollection;

}
