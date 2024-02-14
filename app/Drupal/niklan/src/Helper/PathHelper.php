<?php declare(strict_types = 1);

namespace Drupal\niklan\Helper;

/**
 * Provides system path utils.
 */
final class PathHelper {

  /**
   * Normalize path like standard realpath() does.
   *
   * The main difference is that this implementation doesn't care about file or
   * directory existance, it's just working with path.
   *
   * E.g., 'path/to/something/../../file.md' will be converted to
   * 'path/file.md'.
   *
   * @see https://stackoverflow.com/a/10067975/4751623
   */
  public static function normalizePath(string $path): string {
    $root = $path[0] === \DIRECTORY_SEPARATOR ? \DIRECTORY_SEPARATOR : '';

    $segments = \explode(\DIRECTORY_SEPARATOR, \trim($path, \DIRECTORY_SEPARATOR));
    $ret = [];

    foreach ($segments as $segment) {
      if (($segment === '.') || \strlen($segment) === 0) {
        continue;
      }

      if ($segment === '..') {
        \array_pop($ret);
      }
      else {
        \array_push($ret, $segment);
      }
    }

    return $root . \implode(\DIRECTORY_SEPARATOR, $ret);
  }

}
