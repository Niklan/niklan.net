<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\Contract\Parser\Array\Parser;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Parser\Array\ArrayParseRequest;

final readonly class ArrayParser implements Parser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === Callout::getNodeType();
  }

  public function parse(ArrayParseRequest $request): Node {
    $node = new Callout($request->currentArrayElement->properties['type']);
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));
    return $node;
  }

}
