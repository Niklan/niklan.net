<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Image;

use Drupal\external_content\Contract\Exporter\RenderArray\Builder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Exporter\RenderArray\RenderArrayBuildRequest;

final readonly class RenderArrayBuilder implements Builder {

  public function supports(RenderArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof Image;
  }

  public function build(RenderArrayBuildRequest $request): RenderArray {
    \assert($request->currentAstNode instanceof Image);
    return new RenderArray([
      '#type' => 'html_tag',
      '#tag' => 'img',
      '#attributes' => [
        'src' => $request->currentAstNode->getSrc(),
        'alt' => $request->currentAstNode->getAlt(),
      ],
    ]);
  }

}
