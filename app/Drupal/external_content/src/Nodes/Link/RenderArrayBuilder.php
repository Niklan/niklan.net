<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Link;

use Drupal\external_content\Contract\Exporter\RenderArray\Builder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Exporter\RenderArray\RenderArrayBuildRequest;

final readonly class RenderArrayBuilder implements Builder {

  public function supports(RenderArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof Link;
  }

  public function build(RenderArrayBuildRequest $request): RenderArray {
    \assert($request->currentAstNode instanceof Link);
    return new RenderArray([
      '#type' => 'html_tag',
      '#tag' => 'a',
      '#attributes' => [
        'href' => $request->currentAstNode->getUrl(),
        'rel' => $request->currentAstNode->getRel(),
        'title' => $request->currentAstNode->getTitle(),
      ],
    ]);
  }

}
