<?php

declare(strict_types=1);

namespace Drupal\app_contract\Contract\File;

use Drupal\file\FileInterface;

interface File extends FileInterface {

  public function getChecksum(): ?string;

  public function calculateChecksum(): void;

}
