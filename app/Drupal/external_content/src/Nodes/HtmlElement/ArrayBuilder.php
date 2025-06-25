<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\HtmlElement;

use Drupal\external_content\Contract\Exporter\Array\Builder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Exporter\Array\ArrayBuildRequest;

final readonly class ArrayBuilder implements Builder {

  public function supports(ArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof HtmlElement;
  }

  public function build(ArrayBuildRequest $request): ArrayElement {
    \assert($request->currentAstNode instanceof HtmlElement);
    return new ArrayElement($request->currentAstNode::getType(), ['tag' => $request->currentAstNode->tag]);
  }

}
