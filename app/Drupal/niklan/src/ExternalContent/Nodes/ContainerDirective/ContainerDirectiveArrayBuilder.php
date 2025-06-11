<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\ContainerDirective;

use Drupal\external_content\Contract\Exporter\ArrayElementBuilder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Exporter\Array\ArrayBuildRequest;

final class ContainerDirectiveArrayBuilder implements ArrayElementBuilder {

  public function supports(ArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof ContainerDirectiveNode;
  }

  public function build(ArrayBuildRequest $request): ArrayElement {
    $node = $request->currentAstNode;
    return new ArrayElement(
      ContainerDirectiveNode::getType(),
      ['directiveType' => $node->directiveType] + $node->getProperties()->all(),
    );
  }

}
