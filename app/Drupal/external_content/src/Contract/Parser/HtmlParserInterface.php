<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Parser;

use Drupal\external_content\Data\HtmlParserResult;

/**
 * Provides an interface for HTML parsers.
 */
interface HtmlParserInterface {

  /**
   * {@selfdoc}
   */
  public function parseNode(\DOMNode $node, ChildHtmlParserInterface $child_parser): HtmlParserResult;

}
