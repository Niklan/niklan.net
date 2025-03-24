<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array\Builder;

use Drupal\external_content\Contract\Exporter\ArrayElementBuilder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\DataStructure\Nodes\CodeNode;

final readonly class CodeNodeBuilder implements ArrayElementBuilder {

  public function supports(ArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof CodeNode;
  }

  public function build(ArrayBuildRequest $request): ArrayElement {
    \assert($request->currentAstNode instanceof CodeNode);
    $element = new ArrayElement(
      $request->currentAstNode->getType(),
      ['literal' => $request->currentAstNode->getLiteral()],
    );
    $request->exportRequest->getArrayStructureBuilder()->buildChildren($request->withNewArrayElement($element));

    return $element;
  }

}
