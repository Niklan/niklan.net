<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\Contract\Importer\Array\Parser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final readonly class CalloutBodyArrayParser implements Parser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === CalloutBodyNode::getType();
  }

  public function parse(ArrayParseRequest $request): Content {
    $node = new CalloutBodyNode();
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));
    return $node;
  }

}
