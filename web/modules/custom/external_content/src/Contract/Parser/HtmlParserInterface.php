<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Parser;

use Drupal\external_content\Data\HtmlParserResult;

/**
 * Represents an external content HTML parser.
 */
interface HtmlParserInterface {

  /**
   * Parse a single element.
   *
   * @param \DOMNode $node
   *   The DOM element to parse.
   *
   * @return \Drupal\external_content\Data\HtmlParserResult
   *   The result of parse.
   */
  public function parse(\DOMNode $node): HtmlParserResult;

}
