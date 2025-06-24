<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\HtmlElement;

use Drupal\external_content\Contract\Exporter\ContentArrayElementBuilder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Exporter\Array\BuildRequest;

final readonly class HtmlElementContentArrayBuilder implements ContentArrayElementBuilder {

  public function supports(BuildRequest $request): bool {
    return $request->currentAstNode instanceof HtmlElementNode;
  }

  public function build(BuildRequest $request): ArrayElement {
    \assert($request->currentAstNode instanceof HtmlElementNode);
    return new ArrayElement($request->currentAstNode::getType(), ['tag' => $request->currentAstNode->tag]);
  }

}
