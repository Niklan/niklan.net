<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\CalloutBody;

use Drupal\external_content\Contract\Importer\Array\Parser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final readonly class ArrayParser implements Parser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === CalloutBody::getType();
  }

  public function parse(ArrayParseRequest $request): Content {
    $node = new CalloutBody();
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));
    return $node;
  }

}
