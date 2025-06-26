<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\MediaReference;

use Drupal\external_content\Contract\Importer\Array\Parser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final readonly class ArrayParser implements Parser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === MediaReference::getType();
  }

  public function parse(ArrayParseRequest $request): Content {
    $node = new MediaReference($request->currentArrayElement->properties['uuid']);
    foreach ($request->currentArrayElement->properties as $name => $value) {
      if ($name === 'uuid') {
        continue;
      }
      $node->getProperties()->setProperty($name, $value);
    }
    return $node;
  }

}
