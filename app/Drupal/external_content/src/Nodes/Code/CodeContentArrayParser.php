<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Code;

use Drupal\external_content\Contract\Importer\ContentArrayElementParser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final readonly class CodeContentArrayParser implements ContentArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === CodeNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    return new CodeNode($request->currentArrayElement->properties['code']);
  }

}
