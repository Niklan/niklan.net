<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\HtmlElement;

use Drupal\external_content\Contract\DataStructure\HtmlNodeParser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final class HtmlElementHtmlParser implements HtmlNodeParser {

  public function supports(HtmlParseRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMNode;
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    $element = new HtmlElementNode($request->currentHtmlNode->nodeName);
    if ($request->currentHtmlNode->hasAttributes()) {
      foreach ($request->currentHtmlNode->attributes as $attribute) {
        \assert($attribute instanceof \DOMAttr);
        $element->getProperties()->setProperty($attribute->name, $attribute->value);
      }
    }
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($element));
    return $element;
  }

}
