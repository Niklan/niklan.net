<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Heading;

use Drupal\external_content\Contract\Exporter\RenderArray\Builder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Exporter\RenderArray\RenderArrayBuildRequest;

final readonly class RenderArrayBuilder implements Builder {

  public function supports(RenderArrayBuildRequest $request): bool {
    return $request->node instanceof Heading;
  }

  public function build(RenderArrayBuildRequest $request): RenderArray {
    \assert($request->node instanceof Heading);
    $element = new RenderArray([
      '#type' => 'html_tag',
      '#tag' => $request->node->tagType->value,
    ]);
    $request->request->getRenderArrayBuilder()->buildChildren($request->withNewRenderArray($element));
    return $element;
  }

}
