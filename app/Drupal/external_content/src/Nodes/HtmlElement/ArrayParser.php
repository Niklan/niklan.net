<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\HtmlElement;

use Drupal\external_content\Contract\Importer\Array\Parser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final readonly class ArrayParser implements Parser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === HtmlElement::getType();
  }

  public function parse(ArrayParseRequest $request): Content {
    $element = new HtmlElement(
      tag: $request->currentArrayElement->properties['tag'],
      attributes: $request->currentArrayElement->properties['attributes'] ?? [],
    );
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($element));
    return $element;
  }

}
