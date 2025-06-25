<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\ImporterContext;
use Psr\Log\LoggerInterface;

final readonly class HtmlImporterContext implements ImporterContext {

  public function __construct(
    private LoggerInterface $logger,
  ) {}

  public function getLogger(): LoggerInterface {
    return $this->logger;
  }

}
