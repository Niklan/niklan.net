<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Parser;

use Drupal\external_content\Node\Content;
use Drupal\external_content\Source\File;

/**
 * Provides an interface for external content file parser.
 *
 * Parser is responsible for converting whatever source for content are into
 * a structured class for the module.
 */
interface ParserInterface {

  /**
   * {@selfdoc}
   */
  public function parse(File $file): Content;

}
