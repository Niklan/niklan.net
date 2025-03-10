<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Importer;

interface ImporterSource {

  public function getSourceData(): mixed;

}
