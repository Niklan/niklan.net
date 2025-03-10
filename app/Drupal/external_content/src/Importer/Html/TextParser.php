<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\TextNode;

final class TextParser implements HtmlNodeParser {

  public function supports(\DOMNode $node, HtmlImporterContext $context): bool {
    if (!$node instanceof \DOMText) {
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

  public function parse(\DOMNode $node, HtmlImporterContext $context): ContentNode {
    \assert($node instanceof \DOMText);

    return new TextNode($node->nodeValue ?? '');
  }

}
