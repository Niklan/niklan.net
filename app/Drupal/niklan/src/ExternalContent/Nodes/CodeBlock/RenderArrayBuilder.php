<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\CodeBlock;

use Drupal\external_content\Contract\Exporter\RenderArray\Builder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Exporter\RenderArray\RenderArrayBuildRequest;

final readonly class RenderArrayBuilder implements Builder {

  public function supports(RenderArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof CodeBlock;
  }

  public function build(RenderArrayBuildRequest $request): RenderArray {
    \assert($request->currentAstNode instanceof CodeBlock);
    $info = \json_decode($request->currentAstNode->attributes()->get('data-info') ?? '');
    return new RenderArray([
      '#type' => 'component',
      '#component' => 'niklan:code-block',
      '#props' => [
        'language' => $request->currentAstNode->attributes()->get('data-language'),
        'highlighted_lines' => $info->highlighted_lines ?? NULL,
        'heading' => $info->header ?? NULL,
        'code' => $request->currentAstNode->getCode(),
      ],
    ]);
  }

}
