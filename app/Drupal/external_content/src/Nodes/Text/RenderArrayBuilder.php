<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Text;

use Drupal\external_content\Contract\Exporter\RenderArray\Builder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Exporter\RenderArray\RenderArrayBuildRequest;

final readonly class RenderArrayBuilder implements Builder {

  public function supports(RenderArrayBuildRequest $request): bool {
    return $request->node instanceof Text;
  }

  public function build(RenderArrayBuildRequest $request): RenderArray {
    \assert($request->node instanceof Text);
    return new RenderArray([
      '#markup' => $request->node->text,
    ]);
  }

}
