<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array\Parser;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\DataStructure\Nodes\ContentNode;
use Drupal\external_content\DataStructure\Nodes\HeadingNode;
use Drupal\external_content\Domain\HeadingTagType;

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
