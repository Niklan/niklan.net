<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Parser;

/**
 * Represents an external content HTML parser.
 */
interface HtmlParserInterface {

  /**
   * @todo
   */
  public function parse(\DOMNode $node): void;

}
