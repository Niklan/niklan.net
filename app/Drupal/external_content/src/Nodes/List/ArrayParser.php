<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\List;

use Drupal\external_content\Contract\Importer\Array\Parser;
use Drupal\external_content\Domain\ListType;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final readonly class ArrayParser implements Parser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === [];
  }

  public function parse(ArrayParseRequest $request): Content {
    $node = new List(ListType::from($request->currentArrayElement->properties['type']));
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));
    return $node;
  }

}
