<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\HtmlParser;

use Drupal\external_content\Dto\ElementInterface;

/**
 * Represents an interface for HTML parser plugins.
 */
interface HtmlParserInterface {

  /**
   * Determines is plugin is able to parse provided DOM node.
   *
   * @param \DOMNode $node
   *   The DOM node.
   *
   * @return bool
   *   TRUE if plugin is able to parse it, FALSE otherwise.
   */
  public static function isApplicable(\DOMNode $node): bool;

  /**
   * Parses a single HTML node.
   *
   * @param \DOMNode $node
   *   The DOM node to parse.
   *
   * @return \Drupal\external_content\Dto\ElementInterface
   *   The parsed element with its children.
   */
  public function parse(\DOMNode $node): ElementInterface;

}
