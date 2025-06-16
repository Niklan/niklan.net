<?php

declare(strict_types=1);

namespace Drupal\niklan\Media\Synchronizer;

use Drupal\media\MediaInterface;
use Drupal\niklan\File\Contract\FileSynchronizer;
use Drupal\niklan\Media\Contract\MediaSynchronizer;

final readonly class DatabaseMediaSynchronizer implements MediaSynchronizer {

  public function __construct(
    private FileSynchronizer $fileSynchronizer,
  ) {}

  public function sync(string $path): ?MediaInterface {
    // TODO: Implement sync() method.
  }

}
