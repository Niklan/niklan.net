<?php

declare(strict_types=1);

namespace Drupal\niklan\File\Contract;

use Drupal\niklan\File\Entity\FileInterface;

interface FileSynchronizer {

  public function sync(string $path): ?FileInterface;

}
