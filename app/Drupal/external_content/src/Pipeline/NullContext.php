<?php

declare(strict_types=1);

namespace Drupal\external_content\Pipeline;

use Drupal\external_content\Contract\Pipeline\Context;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final readonly class NullContext implements Context {

  private LoggerInterface $logger;

  public function getLogger(): LoggerInterface {
    if (!$this->logger) {
      $this->logger = new NullLogger();
    }

    return $this->logger;
  }

}
