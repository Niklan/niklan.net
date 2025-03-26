<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array\Builder;

use Drupal\external_content\Contract\Exporter\ArrayElementBuilder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\DataStructure\Nodes\ElementNode;

final readonly class ElementNodeBuilder implements ArrayElementBuilder {

  public function supports(ArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof ElementNode;
  }

  public function build(ArrayBuildRequest $request): ArrayElement {
    \assert($request->currentAstNode instanceof ElementNode);
    $element = new ArrayElement($request->currentAstNode::getType(), $request->currentAstNode->getProperties()->all());
    $request->exportRequest->getArrayStructureBuilder()->buildChildren($request->withNewArrayElement($element));

    return $element;
  }

}
