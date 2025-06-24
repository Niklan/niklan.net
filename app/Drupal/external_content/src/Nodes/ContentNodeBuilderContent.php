<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes;

use Drupal\external_content\Contract\Exporter\ContentArrayElementBuilder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Exporter\Array\BuildRequest;

final readonly class ContentNodeBuilderContent implements ContentArrayElementBuilder {

  public function supports(BuildRequest $request): bool {
    return $request->currentAstNode instanceof ContentNode;
  }

  public function build(BuildRequest $request): ArrayElement {
    \assert($request->currentAstNode instanceof ContentNode);
    $element = new ArrayElement($request->currentAstNode::getType(), $request->currentAstNode->getProperties()->all());
    $request->exportRequest->getArrayStructureBuilder()->buildChildren($request->withNewArrayElement($element));
    return $element;
  }

}
