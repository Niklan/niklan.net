<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Exporter\Array;

use Drupal\external_content\Contract\Exporter\ArrayElementBuilder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Exporter\Array\Builder\ArrayBuildRequest;
use Drupal\niklan\ExternalContent\DataStructure\Nodes\RemoteVideoNode;

final readonly class RemoteVideoBuilder implements ArrayElementBuilder {

  public function supports(ArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof RemoteVideoNode;
  }

  public function build(ArrayBuildRequest $request): ArrayElement {
    \assert($request->currentAstNode instanceof RemoteVideoNode);

    return new ArrayElement($request->currentAstNode::getType(), ['url' => $request->currentAstNode->videoUrl]);
  }

}
