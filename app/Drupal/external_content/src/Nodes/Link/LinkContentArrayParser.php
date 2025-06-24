<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Link;

use Drupal\external_content\Contract\Importer\ContentArrayElementParser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final readonly class LinkContentArrayParser implements ContentArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === LinkNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    $node = new LinkNode(
      $request->currentArrayElement->properties['url'],
      $request->currentArrayElement->properties['target'] ?? NULL,
      $request->currentArrayElement->properties['rel'] ?? NULL,
      $request->currentArrayElement->properties['title'] ?? NULL,
    );
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));

    return $node;
  }

}
