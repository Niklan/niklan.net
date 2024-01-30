<?php declare(strict_types = 1);

namespace Drupal\niklan\Helper;

/**
 * {@selfdoc}
 */
final class FileHelper {

  /**
   * {@selfdoc}
   */
  public static function fileChecksum(string $uri): ?string {
    if (!\file_exists($uri)) {
      return NULL;
    }

    return \md5_file($uri);
  }

}
