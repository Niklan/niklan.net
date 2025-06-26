<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Paragraph;

use Drupal\external_content\Contract\Exporter\RenderArray\Builder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Exporter\RenderArray\RenderArrayBuildRequest;

final readonly class RenderArrayBuilder implements Builder {

  public function supports(RenderArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof Paragraph;
  }

  public function build(RenderArrayBuildRequest $request): RenderArray {
    \assert($request->currentAstNode instanceof Paragraph);
    $element = new RenderArray([
      '#type' => 'html_tag',
      '#tag' => 'p',
    ]);
    $request->exportRequest->getRenderArrayBuilder()->buildChildren($request->withNewRenderArray($element));
    return $element;
  }

}
