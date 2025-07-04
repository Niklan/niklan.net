<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\HtmlElement;

use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Parser\Html\HtmlParseRequest;

final class HtmlParser implements Parser {

  public function supports(HtmlParseRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMNode;
  }

  public function parse(HtmlParseRequest $request): Node {
    $element = new HtmlElement($request->currentHtmlNode->nodeName, $this->parseAttributes($request));
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($element));
    return $element;
  }

  private function parseAttributes(HtmlParseRequest $request): array {
    $attributes = [];
    if ($request->currentHtmlNode->hasAttributes()) {
      foreach ($request->currentHtmlNode->attributes as $attribute) {
        \assert($attribute instanceof \DOMAttr);
        $attributes[$attribute->name] = $attribute->value;
      }
    }
    return $attributes;
  }

}
