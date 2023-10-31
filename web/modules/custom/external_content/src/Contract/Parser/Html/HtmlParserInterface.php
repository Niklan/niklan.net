<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Parser\Html;

/**
 * Provides an interface for HTML parsers.
 */
interface HtmlParserInterface {

  /**
   * {@selfdoc}
   */
  public function parseNode(\DOMNode $node): HtmlParserResultInterface;

}
