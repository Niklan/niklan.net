<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\CodeBlock;

use Drupal\external_content\Contract\Importer\Html\Parser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final class HtmlParser implements Parser {

  public function supports(HtmlParseRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMElement) {
      return FALSE;
    }
    return $request->currentHtmlNode->nodeName === 'pre' && $request->currentHtmlNode->firstChild?->nodeName === 'code';
  }

  public function parse(HtmlParseRequest $request): Content {
    \assert($request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->firstChild instanceof \DOMElement);
    $attributes = [];
    if ($request->currentHtmlNode->hasAttributes()) {
      foreach ($request->currentHtmlNode->attributes as $attribute) {
        \assert($attribute instanceof \DOMAttr);
        $attributes[$attribute->name] = $attribute->value;
      }
    }
    return new CodeBlock($request->currentHtmlNode->firstChild->textContent, $attributes);
  }

}
