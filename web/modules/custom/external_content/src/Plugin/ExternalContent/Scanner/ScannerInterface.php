<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Scanner;

use Drupal\external_content\Dto\SourceFileCollection;

/**
 * Provides an interface for external content source file scanner.
 */
interface ScannerInterface {

  /**
   * Scans for content source files.
   *
   * @return \Drupal\external_content\Dto\SourceFileCollection
   *   A list of found source files.
   */
  public function scan(): SourceFileCollection;

}
