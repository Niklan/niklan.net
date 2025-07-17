<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Text;

use Drupal\external_content\Contract\Parser\Html\ChildParser;
use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Nodes\Node;

final class HtmlParser implements Parser {

  /**
   * Historical context: Empty string check was intentionally removed.
   *
   * Previously contained:
   * @code
   * return trim($nodeValue) !== '';
   * @endcode
   *
   * Why removed:
   * 1. Caused incorrect space handling between inline elements:
   *    @code
   *    <a>Foo</a> <code>Bar</code> → space between elements was lost
   *    @endcode
   * 2. Text node processing should be context-agnostic
   *
   * Do NOT reintroduce this check:
   * - Whitespace handling must be managed by parent parser
   * - Empty DOMText nodes are valid and should be processed
   */
  public function supports(\DOMNode $dom_node): bool {
    return $dom_node instanceof \DOMText;
  }

  public function parseElement(\DOMNode $dom_node, ChildParser $child_parser): Node {
    \assert($dom_node instanceof \DOMText);
    return new Text($dom_node->nodeValue ?? '');
  }

}
