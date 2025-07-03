<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\CodeBlock;

use Drupal\external_content\Contract\Exporter\RenderArray\Builder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Exporter\RenderArray\RenderArrayBuildRequest;

final readonly class RenderArrayBuilder implements Builder {

  public function supports(RenderArrayBuildRequest $request): bool {
    return $request->node instanceof CodeBlock;
  }

  public function build(RenderArrayBuildRequest $request): RenderArray {
    \assert($request->node instanceof CodeBlock);
    $info = \json_decode($request->node->attributes['data-info'] ?? '');
    return new RenderArray([
      '#type' => 'component',
      '#component' => 'niklan:code-block',
      '#props' => [
        'language' => $request->node->attributes['data-language'],
        'highlighted_lines' => $info->highlighted_lines ?? NULL,
        'heading' => $info->header ?? NULL,
        'code' => $request->node->code,
      ],
    ]);
  }

}
