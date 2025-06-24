<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Importer;

use Psr\Log\LoggerInterface;

interface ContentImporterContext {

  public function getLogger(): LoggerInterface;

}
