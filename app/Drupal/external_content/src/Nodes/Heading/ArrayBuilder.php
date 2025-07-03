<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Heading;

use Drupal\external_content\Contract\Exporter\Array\Builder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Exporter\Array\ArrayBuildRequest;

final readonly class ArrayBuilder implements Builder {

  public function supports(ArrayBuildRequest $request): bool {
    return $request->node instanceof Heading;
  }

  public function build(ArrayBuildRequest $request): ArrayElement {
    \assert($request->node instanceof Heading);
    $element = new ArrayElement(
      type: $request->node::getNodeType(),
      properties: [
        'tagType' => $request->node->tagType,
      ],
    );
    $request->request->getArrayStructureBuilder()->buildChildren($request->withNewArrayElement($element));
    return $element;
  }

}
