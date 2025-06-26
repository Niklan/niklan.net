<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Exporter\RenderArray;

use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Exporter\RenderArray\RenderArrayBuildRequest;

interface Builder {

  public function supports(RenderArrayBuildRequest $request): bool;

  public function build(RenderArrayBuildRequest $request): RenderArray;

}
