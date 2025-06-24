<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Exporter;

use Psr\Log\LoggerInterface;

interface ContentExporterContext {

  public function getLogger(): LoggerInterface;

}
