<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Video;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final readonly class VideoArrayParser implements ArrayElementParser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === VideoNode::getType();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    return new VideoNode(
      src: $request->currentArrayElement->properties['src'],
      title: $request->currentArrayElement->properties['title'],
    );
  }

}
