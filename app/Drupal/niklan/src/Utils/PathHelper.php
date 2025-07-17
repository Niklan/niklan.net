<?php

declare(strict_types=1);

namespace Drupal\niklan\Utils;

final class PathHelper {

  /**
   * Normalizes path similar to realpath() without filesystem checks.
   */
  public static function normalizePath(string $path): string {
    $scheme_separator_position = \strpos($path, '://');

    if ($scheme_separator_position !== FALSE) {
      $scheme = \substr($path, 0, $scheme_separator_position);
      $path_after_scheme = \substr($path, $scheme_separator_position + 3);
      $normalized_path = self::normalizePathWithoutScheme($path_after_scheme);
      return "{$scheme}://{$normalized_path}";
    }

    return self::normalizePathWithoutScheme($path);
  }

  private static function normalizePathWithoutScheme(string $path): string {
    $decoded_path = \urldecode($path);
    $is_absolute = $decoded_path !== '' && $decoded_path[0] === \DIRECTORY_SEPARATOR;

    $segments = \explode(\DIRECTORY_SEPARATOR, \trim($decoded_path, \DIRECTORY_SEPARATOR));

    $result_segments = [];
    foreach ($segments as $segment) {
      match ($segment) {
        '', '.' => NULL,
        '..' => \array_pop($result_segments),
        default => \array_push($result_segments, $segment)
      };
    }

    $path_start = $is_absolute ? \DIRECTORY_SEPARATOR : '';
    return $path_start . \implode(\DIRECTORY_SEPARATOR, $result_segments);
  }

}
