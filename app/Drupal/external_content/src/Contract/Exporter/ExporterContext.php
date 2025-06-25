<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Exporter;

use Psr\Log\LoggerInterface;

interface ExporterContext {

  public function getLogger(): LoggerInterface;

}
