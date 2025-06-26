<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\CodeBlock;

use Drupal\external_content\Contract\Exporter\RenderArray\Builder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Exporter\RenderArray\RenderArrayBuildRequest;

final readonly class RenderArrayBuilder implements Builder {

  public function supports(RenderArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof CodeBlock;
  }

  public function build(RenderArrayBuildRequest $request): RenderArray {
    \assert($request->currentAstNode instanceof CodeBlock);
    return new RenderArray([
      '#type' => 'html_tag',
      '#tag' => 'pre',
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
