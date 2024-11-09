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

  public static function extension(string $filename, ?string $suffix = NULL): string {
    $extension = \pathinfo($filename, \PATHINFO_EXTENSION);

    // The ".gz" extension is usually consists of two parts.
    if ($extension === 'gz') {
      $filename = \pathinfo($filename, \PATHINFO_FILENAME);

      // Only continue if filename still contains dot.
      if (\str_contains($filename, '.')) {
        return self::extension($filename, '.gz');
      }
    }

    return $extension . $suffix;
  }

}
