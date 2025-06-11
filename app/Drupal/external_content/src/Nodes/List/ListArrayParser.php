<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\List;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\Domain\ListType;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final readonly class ListArrayParser implements ArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === ListNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    $node = new ListNode(ListType::from($request->currentArrayElement->properties['type']));
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));
    return $node;
  }

}
