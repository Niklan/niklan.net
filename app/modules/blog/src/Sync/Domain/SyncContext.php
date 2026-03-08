<?php

declare(strict_types=1);

namespace Drupal\app_blog\Sync\Domain;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class SyncContext {

  private bool $isForced = FALSE;

  public function __construct(
    public readonly string $workingDirectory,
    public readonly string $contentRoot,
    public readonly LoggerInterface $logger = new NullLogger(),
  ) {}

  public function setForceStatus(bool $status): void {
    $this->isForced = $status;
  }

  public function isForced(): bool {
    return $this->isForced;
  }

}
