<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final readonly class CalloutArrayParser implements ArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === CalloutNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    $node = new CalloutNode($request->currentArrayElement->properties['calloutType']);
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));
    return $node;
  }

}
