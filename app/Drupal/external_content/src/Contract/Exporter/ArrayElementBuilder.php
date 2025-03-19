<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Exporter;

use Drupal\external_content\Exporter\Array\Builder\ArrayElement;
use Drupal\external_content\Exporter\Array\Builder\ArrayBuildRequest;

interface ArrayElementBuilder {

  public function supports(ArrayBuildRequest $request): bool;

  public function build(ArrayBuildRequest $request): ArrayElement;

}
