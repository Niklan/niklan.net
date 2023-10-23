<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Parser;

use Drupal\external_content\Data\ExternalContentFile;

/**
 * Provides an interface for external content file parser.
 */
interface ParserInterface {

  /**
   * {@selfdoc}
   */
  public function parse(ExternalContentFile $file): void;

}
