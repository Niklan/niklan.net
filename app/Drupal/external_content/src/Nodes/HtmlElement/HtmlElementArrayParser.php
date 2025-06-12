<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\HtmlElement;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final readonly class HtmlElementArrayParser implements ArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === HtmlElementNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    return new HtmlElementNode($request->currentArrayElement->properties['tag']);
  }

}
