<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array\Builder;

use Drupal\external_content\Contract\Exporter\ArrayElementBuilder;
use Drupal\external_content\Contract\DataStructure\Nodes\LiteralAware;
use Drupal\external_content\DataStructure\ArrayElement;

final readonly class LiteralNodeBuilder implements ArrayElementBuilder {

  public function supports(ArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof LiteralAware;
  }

  public function build(ArrayBuildRequest $request): ArrayElement {
    \assert($request->currentAstNode instanceof LiteralAware);

    return new ArrayElement(
      $request->currentAstNode::getType(),
      ['literal' => $request->currentAstNode->getLiteral()],
    );
  }

}
