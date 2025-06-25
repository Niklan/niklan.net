<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\LocalVideo;

use Drupal\external_content\Contract\Importer\Array\Parser;
use Drupal\external_content\Importer\Array\ArrayParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final readonly class VideoArrayParser implements Parser {

  public function supports(ArrayParseRequest $request): bool {
    return $request->currentArrayElement->type === VideoNode::getType();
  }

  public function parse(ArrayParseRequest $request): Content {
    return new VideoNode(
      src: $request->currentArrayElement->properties['src'],
      title: $request->currentArrayElement->properties['title'],
    );
  }

}
