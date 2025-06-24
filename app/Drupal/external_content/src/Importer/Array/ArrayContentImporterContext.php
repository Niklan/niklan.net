<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array;

use Drupal\external_content\Contract\Importer\ContentImporterContext;
use Psr\Log\LoggerInterface;

final readonly class ArrayContentImporterContext implements ContentImporterContext {

  public function __construct(
    private LoggerInterface $logger,
  ) {}

  public function getLogger(): LoggerInterface {
    return $this->logger;
  }

}
