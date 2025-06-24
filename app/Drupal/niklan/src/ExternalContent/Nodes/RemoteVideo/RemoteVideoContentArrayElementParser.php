<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\RemoteVideo;

use Drupal\external_content\Contract\Importer\ContentArrayElementParser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final readonly class RemoteVideoContentArrayElementParser implements ContentArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === RemoteVideoNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    return new RemoteVideoNode($request->currentArrayElement->properties['url']);
  }

}
