<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\CodeBlock;

use Drupal\external_content\Contract\Importer\Array\Parser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\Node;

final readonly class ArrayParser implements Parser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === CodeBlock::getNodeType();
  }

  public function parse(ArrayParseRequest $request): Node {
    return new CodeBlock(
      code: $request->currentArrayElement->properties['code'],
      attributes: $request->currentArrayElement->properties['attributes'] ?? [],
    );
  }

}
