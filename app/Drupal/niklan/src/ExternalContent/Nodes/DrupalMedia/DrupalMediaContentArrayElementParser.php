<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\DrupalMedia;

use Drupal\external_content\Contract\Importer\ContentArrayElementParser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final readonly class DrupalMediaContentArrayElementParser implements ContentArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === DrupalMediaNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    $node = new DrupalMediaNode($request->currentArrayElement->properties['uuid']);
    foreach ($request->currentArrayElement->properties as $name => $value) {
      if ($name === 'uuid') {
        continue;
      }
      $node->getProperties()->setProperty($name, $value);
    }
    return $node;
  }

}
