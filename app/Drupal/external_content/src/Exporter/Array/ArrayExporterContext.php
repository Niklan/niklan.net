<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\Array;

use Drupal\external_content\Contract\Exporter\ExporterContext;
use Psr\Log\LoggerInterface;

final readonly class ArrayExporterContext implements ExporterContext {

  public function __construct(
    private LoggerInterface $logger,
  ) {}

  public function getLogger(): LoggerInterface {
    return $this->logger;
  }

}
