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

  private bool $removeMissing = FALSE;

  public function setRemoveMissing(bool $status): void {
    $this->removeMissing = $status;
  }

  public function shouldRemoveMissing(): bool {
    return $this->removeMissing;
  }

  public function setForceStatus(bool $status): void {
    $this->isForced = $status;
  }

  public function isForced(): bool {
    return $this->isForced;
  }

}
