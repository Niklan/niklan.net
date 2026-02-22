<?php

declare(strict_types=1);

namespace Drupal\app_contract\Contract\Media;

use Drupal\media\MediaInterface;

interface MediaSynchronizer {

  public function sync(string $path, array $extra = []): ?MediaInterface;

}
