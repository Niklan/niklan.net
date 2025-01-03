<?php

declare(strict_types=1);

namespace Drupal\niklan\File\Entity;

use Drupal\file\FileInterface as CoreFileInterface;

interface FileInterface extends CoreFileInterface {

  public function getChecksum(): ?string;

  public function calculateChecksum(): void;

}
