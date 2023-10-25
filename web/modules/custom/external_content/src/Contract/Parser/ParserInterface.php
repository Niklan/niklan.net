<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Parser;

use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Node\Content;

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
  public function parse(SourceInterface $source): Content;

  /**
   * {@selfdoc}
   */
  public function supportsParse(SourceInterface $source): bool;

}
