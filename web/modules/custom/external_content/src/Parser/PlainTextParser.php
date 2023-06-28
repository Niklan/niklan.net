<?php declare(strict_types = 1);

namespace Drupal\external_content\Parser;

use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Node\PlainText;

/**
 * Provides a parser for a simple text inside HTML elements.
 *
 * It is important that this parser have higher priority over HtmlElementParser.
 */
final class PlainTextParser implements HtmlParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(\DOMNode $node): HtmlParserResult {
    if (!$node instanceof \DOMText) {
      return HtmlParserResult::continue();
    }

    // If this is a DOMText and trim is an empty value, skip processing element,
    // because it's just a whitespace.
    if (!\trim($node->nodeValue)) {
      return HtmlParserResult::finalize();
    }

    return HtmlParserResult::finalizeWith(new PlainText($node->nodeValue));
  }

}
