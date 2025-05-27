<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\RemoteVideo;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\DataStructure\Nodes\ContentNode;
use Drupal\external_content\Importer\Array\Parser\ArrayParseRequest;

final readonly class RemoteVideoArrayElementParser implements ArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === RemoteVideoNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    return new RemoteVideoNode($request->currentArrayElement->properties['url']);
  }

}
