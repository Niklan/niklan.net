<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\HtmlElement;

use Drupal\external_content\Contract\Exporter\RenderArray\Builder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Exporter\RenderArray\RenderArrayBuildRequest;

final readonly class RenderArrayBuilder implements Builder {

  public function supports(RenderArrayBuildRequest $request): bool {
    return $request->node instanceof HtmlElement;
  }

  public function build(RenderArrayBuildRequest $request): RenderArray {
    \assert($request->node instanceof HtmlElement);
    $element = new RenderArray([
      '#type' => 'html_tag',
      '#tag' => $request->node->tag,
      '#attributes' => $request->node->attributes,
    ]);
    $request->request->getRenderArrayBuilder()->buildChildren($request->withNewRenderArray($element));
    return $element;
  }

}
