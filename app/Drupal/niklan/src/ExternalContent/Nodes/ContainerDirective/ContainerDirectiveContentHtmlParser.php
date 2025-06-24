<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\ContainerDirective;

use Drupal\external_content\Contract\Importer\ContentHtmlParser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final class ContainerDirectiveContentHtmlParser implements ContentHtmlParser {

  public function supports(HtmlParseRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMElement) {
      return FALSE;
    }

    return $request->currentHtmlNode->getAttribute('data-selector') === 'niklan:container-directive';
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    $type = $request->currentHtmlNode->getAttribute('data-type');
    $node = new ContainerDirectiveNode($type);

    if ($request->currentHtmlNode->hasAttributes()) {
      foreach ($request->currentHtmlNode->attributes as $attribute) {
        \assert($attribute instanceof \DOMAttr);
        $node->getProperties()->setProperty($attribute->name, $attribute->value);
      }
    }

    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($node));
    return $node;
  }

}
