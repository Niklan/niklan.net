<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\CodeBlock;

use Drupal\Core\Render\Element\HtmlTag;
use Drupal\external_content\Contract\Exporter\RenderArray\Builder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Exporter\RenderArray\RenderArrayBuildRequest;
use Drupal\external_content\Nodes\Format\RenderArrayBuilder as FormatRenderArrayBuilder;

final readonly class RenderArrayBuilder implements Builder {

  public function supports(RenderArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof CodeBlock;
  }

  public function build(RenderArrayBuildRequest $request): RenderArray {
    \assert($request->currentAstNode instanceof CodeBlock);
    return new RenderArray([
      '#type' => 'html_tag',
      '#tag' => 'pre',
      // Make sure that the processing is consistent with the element builder.
      '#pre_render' => [
        HtmlTag::preRenderHtmlTag(...),
        FormatRenderArrayBuilder::preRenderTag(...),
      ],
      'code' => [
        '#type' => 'html_tag',
        '#tag' => 'code',
        'value' => [
          '#markup' => $request->currentAstNode->getCode(),
        ],
      ],
    ]);
  }

}
