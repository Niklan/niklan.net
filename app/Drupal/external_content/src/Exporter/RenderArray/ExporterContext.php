<?php

declare(strict_types=1);

namespace Drupal\external_content\Exporter\RenderArray;

use Drupal\external_content\Contract\Exporter\ContentExporterContext;
use Psr\Log\LoggerInterface;

final readonly class ExporterContext implements ContentExporterContext {

  public function __construct(
    private LoggerInterface $logger,
  ) {}

  public function getLogger(): LoggerInterface {
    return $this->logger;
  }

}
