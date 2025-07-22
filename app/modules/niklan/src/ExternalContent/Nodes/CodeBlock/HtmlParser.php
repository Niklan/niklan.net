<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\CodeBlock;

use Drupal\external_content\Contract\Parser\Html\ChildParser;
use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Utils\HtmlDomHelper;

final class HtmlParser implements Parser {

  public function supports(\DOMNode $dom_node): bool {
    if (!$dom_node instanceof \DOMElement) {
      return FALSE;
    }
    return $dom_node->nodeName === 'pre' && $dom_node->firstChild?->nodeName === 'code';
  }

  public function parseElement(\DOMNode $dom_node, ChildParser $child_parser): Node {
    \assert($dom_node instanceof \DOMElement && $dom_node->firstChild instanceof \DOMElement);
    return new CodeBlock($dom_node->firstChild->textContent, HtmlDomHelper::parseAttributes($dom_node));
  }

}
