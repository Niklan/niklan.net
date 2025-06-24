<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Text;

use Drupal\external_content\Contract\Importer\ContentArrayElementParser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final readonly class TextContentArrayParser implements ContentArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === TextNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    return new TextNode($request->currentArrayElement->properties['text']);
  }

}
