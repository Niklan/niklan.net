<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\HtmlElement;

use Drupal\external_content\Contract\Parser\Html\ChildParser;
use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Nodes\Node;

final class HtmlParser implements Parser {

  public function supports(\DOMNode $dom_node): bool {
    return TRUE;
  }

  public function parseElement(\DOMNode $dom_node, ChildParser $child_parser): Node {
    $element = new HtmlElement($dom_node->nodeName, $this->parseAttributes($dom_node));
    $child_parser->parseChildren($dom_node, $element);
    return $element;
  }

  private function parseAttributes(\DOMNode $dom_node): array {
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
