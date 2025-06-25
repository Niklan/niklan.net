<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\ContainerDirective;

use Drupal\external_content\Contract\Importer\Array\Parser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final class ContainerDirectiveArrayParser implements Parser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === ContainerDirectiveNode::getType();
  }

  public function parse(ArrayParseRequest $request): Content {
    return new ContainerDirectiveNode(
      $request->currentArrayElement->properties['directiveType'],
    );
  }

}
