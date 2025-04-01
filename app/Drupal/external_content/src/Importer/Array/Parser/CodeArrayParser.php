<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array\Parser;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\DataStructure\Nodes\CodeNode;
use Drupal\external_content\DataStructure\Nodes\ContentNode;

final readonly class CodeArrayParser implements ArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === CodeNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    return new CodeNode($request->currentArrayElement->properties['literal']);
  }

}
