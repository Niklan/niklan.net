<?php declare(strict_types = 1);

namespace Drupal\external_content\Parser\Html;

use Drupal\external_content\Contract\Parser\Html\HtmlParserInterface;
use Drupal\external_content\Contract\Parser\Html\HtmlParserResultInterface;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Node\Html\PlainText;

/**
 * Provides a parser for a simple text inside HTML elements.
 *
 * It is important that this parser have higher priority over HtmlElementParser.
 */
final class PlainTextParser implements HtmlParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parseNode(\DOMNode $node): HtmlParserResultInterface {
    if (!$node instanceof \DOMText) {
      return HtmlParserResult::continue();
    }

    // If this is a DOMText and trim is an empty value, skip processing element,
    // because it's just a whitespace.
    if (!\trim($node->nodeValue)) {
      return HtmlParserResult::stop();
    }

    return HtmlParserResult::finalize(new PlainText($node->nodeValue));
  }

}