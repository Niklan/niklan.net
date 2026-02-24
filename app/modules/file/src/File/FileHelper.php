<?php

declare(strict_types=1);

namespace Drupal\app_file\File;

final class FileHelper {

  public static function checksum(string $uri): ?string {
    if (!\file_exists($uri)) {
      return NULL;
    }
    return \md5_file($uri) ?: NULL;
  }

}
