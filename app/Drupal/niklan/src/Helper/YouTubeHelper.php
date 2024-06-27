<?php

declare(strict_types=1);

namespace Drupal\niklan\Helper;

/**
 * {@selfdoc}
 */
final class YouTubeHelper {

  /**
   * {@selfdoc}
   */
  public static function isYouTubeUrl(string $url): bool {
    return (bool) \preg_match(
      pattern: '/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be|youtube\.com)/',
      subject: $url,
    );
  }

  /**
   * {@selfdoc}
   */
  public static function extractVideoId(string $url): ?string {
    \preg_match(
      "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'<> ]+)/",
      $url,
      $matches,
    );

    return \count($matches) >= 2 ? $matches[1] : NULL;
  }

}
