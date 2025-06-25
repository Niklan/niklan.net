<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Exporter\Array;

use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Exporter\Array\ArrayBuildRequest;

interface Builder {

  public function supports(ArrayBuildRequest $request): bool;

  public function build(ArrayBuildRequest $request): ArrayElement;

}
