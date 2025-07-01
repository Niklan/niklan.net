<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\CodeBlock;

use Drupal\external_content\Contract\Exporter\Array\Builder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Exporter\Array\ArrayBuildRequest;

final readonly class ArrayBuilder implements Builder {

  public function supports(ArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof CodeBlock;
  }

  public function build(ArrayBuildRequest $request): ArrayElement {
    \assert($request->currentAstNode instanceof CodeBlock);
    $element = new ArrayElement(
      type: $request->currentAstNode::getType(),
      properties: [
        'code' => $request->currentAstNode->getCode(),
        'attributes' => $request->currentAstNode->attributes()->all(),
      ],
    );
    $request->exportRequest->getArrayStructureBuilder()->buildChildren($request->withNewArrayElement($element));
    return $element;
  }

}
