<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array\Parser;

use Drupal\external_content\Contract\Importer\Array\Parser\TypedArrayParser;
use Drupal\external_content\DataStructure\Nodes\ContentNode;
use Drupal\external_content\DataStructure\Nodes\ElementNode;

final class ContentArrayParser extends TypedArrayParser {

  public function parse(ArrayParseRequest $request): ContentNode {
    $node = $this->createNode($request->currentArrayElement->type);
    \assert($node instanceof ElementNode);
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));

    return $node;
  }

}
