<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Text;

use Drupal\external_content\Contract\Importer\Html\Parser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\Node;

final class HtmlParser implements Parser {

  public function supports(HtmlParseRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMText) {
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

  public function parse(HtmlParseRequest $request): Node {
    \assert($request->currentHtmlNode instanceof \DOMText);
    return new Text($request->currentHtmlNode->nodeValue ?? '');
  }

}
