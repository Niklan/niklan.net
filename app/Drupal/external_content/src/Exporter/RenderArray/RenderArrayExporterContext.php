<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\RenderArray;

use Drupal\external_content\Contract\Exporter\ExporterContext;
use Psr\Log\LoggerInterface;

final readonly class RenderArrayExporterContext implements ExporterContext {

  public function __construct(
    private LoggerInterface $logger,
  ) {}

  public function getLogger(): LoggerInterface {
    return $this->logger;
  }

}
