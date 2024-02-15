<?php declare(strict_types = 1);

namespace Drupal\niklan\Helper;

/**
 * Provides system path utils.
 */
final class PathHelper {

  /**
   * {@selfdoc}
   */
  // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase
  private const string SCHEME_PLACEHOLDER = '__scheme__';

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
    // Scheme separator should be preserved as is.
    $path = \str_replace(
      search: '://',
      replace: self::SCHEME_PLACEHOLDER,
      subject: $path,
    );
    $root = $path[0] === \DIRECTORY_SEPARATOR ? \DIRECTORY_SEPARATOR : '';
    $segments = \explode(
      separator: \DIRECTORY_SEPARATOR,
      string: \trim($path, \DIRECTORY_SEPARATOR),
    );
    $new_segments = [];

    foreach ($segments as $segment) {
      match ($segment) {
        '', '.' => NULL,
        '..' => \array_pop($new_segments),
        default => \array_push($new_segments, $segment),
      };
    }

    $result = $root . \implode(\DIRECTORY_SEPARATOR, $new_segments);

    return \str_replace(self::SCHEME_PLACEHOLDER, '://', $result);
  }

}
