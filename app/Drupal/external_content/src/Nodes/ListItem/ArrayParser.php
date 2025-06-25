<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\ListItem;

use Drupal\external_content\Contract\Importer\Array\Parser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final readonly class ArrayParser implements Parser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === ListItem::getType();
  }

  public function parse(ArrayParseRequest $request): Content {
    $node = new ListItem();
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));

    return $node;
  }

}
