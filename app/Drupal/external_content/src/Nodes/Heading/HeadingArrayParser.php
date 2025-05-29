<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Heading;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\Domain\HeadingTagType;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final readonly class HeadingArrayParser implements ArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === HeadingNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    $node = new HeadingNode(HeadingTagType::from($request->currentArrayElement->properties['tag']));
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));

    return $node;
  }

}
