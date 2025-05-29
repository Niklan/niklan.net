<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\RemoteVideo;

use Drupal\external_content\Contract\Exporter\ArrayElementBuilder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Exporter\Array\ArrayBuildRequest;

final readonly class RemoteVideoArrayElementBuilder implements ArrayElementBuilder {

  public function supports(ArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof RemoteVideoNode;
  }

  public function build(ArrayBuildRequest $request): ArrayElement {
    \assert($request->currentAstNode instanceof RemoteVideoNode);

    return new ArrayElement($request->currentAstNode::getType(), ['url' => $request->currentAstNode->videoUrl]);
  }

}
