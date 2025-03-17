<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array;

use Drupal\external_content\Contract\Exporter\ArrayElementBuilder;
use Drupal\external_content\Node\ContentNode;

final readonly class ContentNodeBuilder implements ArrayElementBuilder {

  public function supports(ArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof ContentNode;
  }

  public function build(ArrayBuildRequest $request): ArrayElement {
    \assert($request->currentAstNode instanceof ContentNode);
    $element = new ArrayElement($request->currentAstNode->getType());
    $request->exportRequest->getArrayStructureBuilder()->buildChildren($request->withNewArrayElement($element));

    return $element;
  }

}
