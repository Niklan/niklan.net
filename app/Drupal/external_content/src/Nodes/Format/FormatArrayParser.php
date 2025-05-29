<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Format;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\Domain\TextFormatType;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final readonly class FormatArrayParser implements ArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === FormatNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    $node = new FormatNode(TextFormatType::from($request->currentArrayElement->properties['format']));
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));

    return $node;
  }

}
