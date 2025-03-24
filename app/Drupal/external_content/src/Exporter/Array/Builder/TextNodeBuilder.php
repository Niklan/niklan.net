<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array\Builder;

use Drupal\external_content\Contract\Exporter\ArrayElementBuilder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\DataStructure\Nodes\TextNode;

final readonly class TextNodeBuilder implements ArrayElementBuilder {

  public function supports(ArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof TextNode;
  }

  public function build(ArrayBuildRequest $request): ArrayElement {
    \assert($request->currentAstNode instanceof TextNode);

    return new ArrayElement(
      $request->currentAstNode->getType(),
      ['literal' => $request->currentAstNode->getLiteral()],
    );
  }

}
