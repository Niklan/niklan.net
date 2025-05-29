<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes;

use Drupal\external_content\Contract\Exporter\ArrayElementBuilder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Exporter\Array\ArrayBuildRequest;

final readonly class ContentNodeBuilder implements ArrayElementBuilder {

  public function supports(ArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof ContentNode;
  }

  public function build(ArrayBuildRequest $request): ArrayElement {
    \assert($request->currentAstNode instanceof ContentNode);
    $element = new ArrayElement($request->currentAstNode::getType(), $request->currentAstNode->getProperties()->all());
    $request->exportRequest->getArrayStructureBuilder()->buildChildren($request->withNewArrayElement($element));
    return $element;
  }

}
