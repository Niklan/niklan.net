<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Importer;

use Drupal\external_content\Node\RootNode;

interface Importer {

  public function import(ImportRequest $request): RootNode;

}
