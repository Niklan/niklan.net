<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\HtmlElement;

use Drupal\external_content\Contract\Exporter\ArrayElementBuilder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Exporter\Array\ArrayBuildRequest;

final readonly class HtmlElementArrayBuilder implements ArrayElementBuilder {

  public function supports(ArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof HtmlElementNode;
  }

  public function build(ArrayBuildRequest $request): ArrayElement {
    \assert($request->currentAstNode instanceof HtmlElementNode);
    return new ArrayElement($request->currentAstNode::getType(), ['tag' => $request->currentAstNode->tag]);
  }

}
