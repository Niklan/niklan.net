<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Image;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final readonly class ImageArrayParser implements ArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === ImageNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    return new ImageNode(
      $request->currentArrayElement->properties['src'],
      $request->currentArrayElement->properties['alt'],
    );
  }

}
