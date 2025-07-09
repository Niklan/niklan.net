<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Text;

use Drupal\external_content\Contract\Parser\Html\ChildParser;
use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Nodes\Node;

final class HtmlParser implements Parser {

  public function supports(\DOMNode $dom_node): bool {
    if (!$dom_node instanceof \DOMText) {
      return FALSE;
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
    return TRUE;
  }

  public function parseElement(\DOMNode $dom_node, ChildParser $child_parser): Node {
    \assert($dom_node instanceof \DOMText);
    return new Text($dom_node->nodeValue ?? '');
  }

}
