<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\CodeBlock;

use Drupal\external_content\Contract\Parser\Html\ChildParser;
use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Nodes\Node;

final class HtmlParser implements Parser {

  public function supports(\DOMNode $dom_node): bool {
    if (!$dom_node instanceof \DOMElement) {
      return FALSE;
    }
    return $dom_node->nodeName === 'pre' && $dom_node->firstChild?->nodeName === 'code';
  }

  public function parseElement(\DOMNode $dom_node, ChildParser $child_parser): Node {
    \assert($dom_node instanceof \DOMElement && $dom_node->firstChild instanceof \DOMElement);
    return new CodeBlock($dom_node->firstChild->textContent, $this->parseAttributes($dom_node));
  }

  public function parseAttributes(\DOMNode $dom_node): array {
    $attributes = [];
    if ($dom_node->hasAttributes()) {
      foreach ($dom_node->attributes as $attribute) {
        \assert($attribute instanceof \DOMAttr);
        $attributes[$attribute->name] = $attribute->value;
      }
    }
    return $attributes;
  }

}
