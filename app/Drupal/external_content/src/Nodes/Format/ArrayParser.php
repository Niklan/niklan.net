<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Format;

use Drupal\external_content\Contract\Parser\Array\Parser;
use Drupal\external_content\Domain\TextFormatType;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Parser\Array\ArrayParseRequest;

final readonly class ArrayParser implements Parser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === Format::getNodeType();
  }

  public function parse(ArrayParseRequest $request): Node {
    $node = new Format(TextFormatType::from($request->currentArrayElement->properties['format']));
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));

    return $node;
  }

}
