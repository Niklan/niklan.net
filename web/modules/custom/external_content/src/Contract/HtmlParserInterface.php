<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

/**
 * Provides an interface for HTML parser.
 */
interface HtmlParserInterface {

  public function parse(\DOMNode $node): void;

}
