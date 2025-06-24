<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\ContainerDirective;

use Drupal\external_content\Contract\Importer\ContentArrayElementParser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final class ContainerDirectiveContentArrayParser implements ContentArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === ContainerDirectiveNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    return new ContainerDirectiveNode(
      $request->currentArrayElement->properties['directiveType'],
    );
  }

}
