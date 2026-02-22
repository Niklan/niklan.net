<?php

declare(strict_types=1);

namespace Drupal\app_contract\Contract\File;

interface FileSynchronizer {

  public function sync(string $path): ?File;

}
