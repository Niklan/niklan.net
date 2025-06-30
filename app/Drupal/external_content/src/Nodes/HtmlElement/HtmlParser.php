<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\HtmlElement;

use Drupal\external_content\Contract\Importer\Html\Parser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final class HtmlParser implements Parser {

  public function supports(HtmlParseRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMNode;
  }

  public function parse(HtmlParseRequest $request): Content {
    $element = new HtmlElement($request->currentHtmlNode->nodeName);
    if ($request->currentHtmlNode->hasAttributes()) {
      foreach ($request->currentHtmlNode->attributes as $attribute) {
        \assert($attribute instanceof \DOMAttr);
        $element->attributes()->set($attribute->name, $attribute->value);
      }
    }
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($element));
    return $element;
  }

}
