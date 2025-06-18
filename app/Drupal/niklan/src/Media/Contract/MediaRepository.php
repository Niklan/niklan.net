<?php

declare(strict_types=1);

namespace Drupal\niklan\Media\Contract;

use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;

interface MediaRepository {

  public function findByFile(FileInterface $file): ?MediaInterface;

  public function findBySourceField(string $bundle, string $source_field, string $value): ?MediaInterface;

}
