<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\ListItem;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final readonly class ListItemArrayParser implements ArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === ListItemNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    $node = new ListItemNode();
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));

    return $node;
  }

}
