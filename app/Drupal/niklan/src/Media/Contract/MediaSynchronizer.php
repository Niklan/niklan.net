<?php

declare(strict_types=1);

namespace Drupal\niklan\Media\Contract;

use Drupal\media\MediaInterface;

interface MediaSynchronizer {

  public function sync(string $path, array $extra = []): ?MediaInterface;

}
