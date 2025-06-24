<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Exporter;

use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Exporter\Array\BuildRequest;

interface ContentArrayElementBuilder {

  public function supports(BuildRequest $request): bool;

  public function build(BuildRequest $request): ArrayElement;

}
