<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\Contract\Exporter\RenderArray\Builder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Exporter\RenderArray\RenderArrayBuildRequest;

final readonly class RenderArrayBuilder implements Builder {

  public function supports(RenderArrayBuildRequest $request): bool {
    return $request->node instanceof Callout;
  }

  public function build(RenderArrayBuildRequest $request): RenderArray {
    \assert($request->node instanceof Callout);
    $build = [
      '#type' => 'component',
      '#component' => 'niklan:callout',
      '#props' => [
        'type' => $request->node->type,
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
    \assert($request->node instanceof Callout);
    if (!$request->node->getBody()) {
      return NULL;
    }

    $result = new RenderArray();
    $child_request = $request->withNewNodeAndRenderArray($request->node->getBody(), $result);
    $request->request->getRenderArrayBuilder()->buildChildren($child_request);
    return $result->toRenderArray();
  }

  private function prepareTitle(RenderArrayBuildRequest $request): ?array {
    \assert($request->node instanceof Callout);
    if (!$request->node->getTitle()) {
      return NULL;
    }

    $result = new RenderArray();
    $child_request = $request->withNewNodeAndRenderArray($request->node->getTitle(), $result);
    $request->request->getRenderArrayBuilder()->buildChildren($child_request);
    return $result->toRenderArray();
  }

}
