<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\Contract\Exporter\RenderArray\Builder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Exporter\RenderArray\RenderArrayBuildRequest;

final readonly class RenderArrayBuilder implements Builder {

  public function supports(RenderArrayBuildRequest $request): bool {
    return $request->currentAstNode instanceof Callout;
  }

  public function build(RenderArrayBuildRequest $request): RenderArray {
    \assert($request->currentAstNode instanceof Callout);
    $build = [
      '#type' => 'component',
      '#component' => 'niklan:callout',
      '#props' => [
        'type' => $request->currentAstNode->getCalloutType(),
      ],
    ];

    $title = $this->prepareTitle($request);
    if ($title) {
      $build['#slots']['title'] = $title;
    }

    $body = $this->prepareBody($request);
    if ($body) {
      $build['#slots']['body'] = $body;
    }

    return new RenderArray($build);
  }

  private function prepareBody(RenderArrayBuildRequest $request): ?array {
    \assert($request->currentAstNode instanceof Callout);
    if (!$request->currentAstNode->getBody()) {
      return NULL;
    }

    $result = new RenderArray();
    $child_request = $request->withNewNodeAndRenderArray($request->currentAstNode->getBody(), $result);
    $request->exportRequest->getRenderArrayBuilder()->buildChildren($child_request);
    return $result->toRenderArray();
  }

  private function prepareTitle(RenderArrayBuildRequest $request): ?array {
    \assert($request->currentAstNode instanceof Callout);
    if (!$request->currentAstNode->getTitle()) {
      return NULL;
    }

    $result = new RenderArray();
    $child_request = $request->withNewNodeAndRenderArray($request->currentAstNode->getTitle(), $result);
    $request->exportRequest->getRenderArrayBuilder()->buildChildren($child_request);
    return $result->toRenderArray();
  }

}
