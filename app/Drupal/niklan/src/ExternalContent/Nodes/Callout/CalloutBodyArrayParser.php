<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final readonly class CalloutBodyArrayParser implements ArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === CalloutBodyNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    $node = new CalloutBodyNode();
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));
    return $node;
  }

}
