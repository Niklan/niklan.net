<?php

declare(strict_types=1);

namespace Drupal\niklan\Utils;

final class PathHelper {

  public static function hashRelativePath(string $path, string $base_path): string {
    return \md5(self::makeRelative($path, $base_path));
  }

  public static function makeRelative(string $path, string $base_path): string {
    $normalized_path = self::normalizePath($path);
    $normalized_base = \rtrim(self::normalizePath($base_path), \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;

    if (!\str_starts_with($normalized_path, $normalized_base)) {
      throw new \InvalidArgumentException(\sprintf(
        'The path "%s" is not within the base path "%s".',
        $normalized_path,
        $normalized_base,
      ));
    }

    // Using substr instead of str_replace to strip only the leading prefix.
    // str_replace would remove all occurrences if the base path appears as a
    // substring deeper in the path.
    return \substr($normalized_path, \strlen($normalized_base));
  }

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
