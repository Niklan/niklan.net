<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\RemoteVideo;

use Drupal\external_content\Contract\Importer\Array\Parser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final readonly class RemoteVideoParser implements Parser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === RemoteVideoNode::getType();
  }

  public function parse(ArrayParseRequest $request): Content {
    return new RemoteVideoNode($request->currentArrayElement->properties['url']);
  }

}
