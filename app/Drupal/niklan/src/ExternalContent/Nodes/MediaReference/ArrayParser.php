<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\MediaReference;

use Drupal\external_content\Contract\Parser\Array\Parser;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Parser\Array\ArrayParseRequest;

final readonly class ArrayParser implements Parser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === MediaReference::getNodeType();
  }

  public function parse(ArrayParseRequest $request): Node {
    return new MediaReference(
      uuid: $request->currentArrayElement->properties['uuid'],
      metadata: $request->currentArrayElement->properties['metadata'] ?? [],
    );
  }

}
