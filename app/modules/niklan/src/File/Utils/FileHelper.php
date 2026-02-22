<?php

declare(strict_types=1);

namespace Drupal\niklan\File\Utils;

final class FileHelper {

  public static function checksum(string $uri): ?string {
    if (!\file_exists($uri)) {
      return NULL;
    }
    return \md5_file($uri) ?: NULL;
  }

}
