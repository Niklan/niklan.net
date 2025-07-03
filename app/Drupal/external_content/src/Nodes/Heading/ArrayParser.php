<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Heading;

use Drupal\external_content\Contract\Importer\Array\Parser;
use Drupal\external_content\Domain\HeadingTagType;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\Node;

final readonly class ArrayParser implements Parser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === Heading::getNodeType();
  }

  public function parse(ArrayParseRequest $request): Node {
    $node = new Heading(HeadingTagType::from($request->currentArrayElement->properties['tagType']));
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));

    return $node;
  }

}
