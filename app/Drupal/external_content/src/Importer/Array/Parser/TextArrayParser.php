<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array\Parser;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\DataStructure\Nodes\ContentNode;
use Drupal\external_content\DataStructure\Nodes\TextNode;

final readonly class TextArrayParser implements ArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === TextNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    return new TextNode($request->currentArrayElement->properties['literal']);
  }

}
