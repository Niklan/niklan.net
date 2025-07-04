<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\CodeBlock;

use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Parser\Html\HtmlParseRequest;

final class HtmlParser implements Parser {

  public function supports(HtmlParseRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMElement) {
      return FALSE;
    }
    return $request->currentHtmlNode->nodeName === 'pre' && $request->currentHtmlNode->firstChild?->nodeName === 'code';
  }

  public function parse(HtmlParseRequest $request): Node {
    \assert($request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->firstChild instanceof \DOMElement);
    return new CodeBlock($request->currentHtmlNode->firstChild->textContent, $this->parseAttributes($request));
  }

  public function parseAttributes(HtmlParseRequest $request): array {
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
