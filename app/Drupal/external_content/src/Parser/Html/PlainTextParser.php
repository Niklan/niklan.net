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

    // Previously, here was a check for an empty string (e.g. space character).
    // It was checked by trim() function and parser returned stop signal for
    // that element. This is wrong, because in cases when multiple consecutive
    // inline HTML elements are added, this logic will fail.
    //
    // Example:
    // @code
    //   <a href="#">foo</a> <code>bar</code>
    //                      ^
    //                      this space is lost, and they are concatenated.
    // @endcode
    //
    // The result was:
    // @code
    //   <a href="#">foo</a><code>bar</code>
    // @endcode
    //
    // This is clearly unwanted behavior. Make sure not to add this check here
    // again.
    return HtmlParserResult::finalize(new PlainText($node->nodeValue));
  }

}
