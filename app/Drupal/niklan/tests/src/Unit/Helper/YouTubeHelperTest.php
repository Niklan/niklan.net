<?php

declare(strict_types=1);

namespace Drupal\niklan\Helper;

use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\niklan\Helper\YouTubeHelper
 */
final class YouTubeHelperTest extends UnitTestCase {

  /**
   * @dataProvider youTubeUrlsProvider
   */
  public function testHelper(string $url, bool $is_youtube_url, ?string $video_id): void {
    self::assertSame($is_youtube_url, YouTubeHelper::isYouTubeUrl($url));
    self::assertSame($video_id, YouTubeHelper::extractVideoId($url));
  }

  public function youTubeUrlsProvider(): \Generator {
    // https://www.youtube.com/watch?v=Y1I7zGn6F-w
    yield [
      'url' => 'https://google.com',
      'is_youtube_url' => FALSE,
      'video_id' => NULL,
    ];

    yield [
      'url' => 'https://www.youtube.com',
      'is_youtube_url' => TRUE,
      'video_id' => NULL,
    ];

    yield [
      'url' => 'https://www.youtube.com/watch?v=vOO-5QGhZgs',
      'is_youtube_url' => TRUE,
      'video_id' => 'vOO-5QGhZgs',
    ];

    yield [
      'url' => 'http://www.youtube.com',
      'is_youtube_url' => TRUE,
      'video_id' => NULL,
    ];

    yield [
      'url' => 'http://www.youtube.com/watch?v=vOO-5QGhZgs',
      'is_youtube_url' => TRUE,
      'video_id' => 'vOO-5QGhZgs',
    ];

    yield [
      'url' => 'https://youtube.com',
      'is_youtube_url' => TRUE,
      'video_id' => NULL,
    ];

    yield [
      'url' => 'https://youtube.com/watch?v=vOO-5QGhZgs',
      'is_youtube_url' => TRUE,
      'video_id' => 'vOO-5QGhZgs',
    ];

    yield [
      'url' => 'http://youtube.com',
      'is_youtube_url' => TRUE,
      'video_id' => NULL,
    ];

    yield [
      'url' => 'http://youtube.com/watch?v=vOO-5QGhZgs',
      'is_youtube_url' => TRUE,
      'video_id' => 'vOO-5QGhZgs',
    ];

    yield [
      'url' => 'youtube.com',
      'is_youtube_url' => TRUE,
      'video_id' => NULL,
    ];

    yield [
      'url' => 'youtube.com/watch?v=vOO-5QGhZgs',
      'is_youtube_url' => TRUE,
      'video_id' => 'vOO-5QGhZgs',
    ];

    yield [
      'url' => 'https://www.youtu.be',
      'is_youtube_url' => TRUE,
      'video_id' => NULL,
    ];

    yield [
      'url' => 'https://www.youtu.be/vOO-5QGhZgs',
      'is_youtube_url' => TRUE,
      'video_id' => 'vOO-5QGhZgs',
    ];

    yield [
      'url' => 'http://www.youtu.be',
      'is_youtube_url' => TRUE,
      'video_id' => NULL,
    ];

    yield [
      'url' => 'http://www.youtu.be/vOO-5QGhZgs',
      'is_youtube_url' => TRUE,
      'video_id' => 'vOO-5QGhZgs',
    ];

    yield [
      'url' => 'https://youtu.be',
      'is_youtube_url' => TRUE,
      'video_id' => NULL,
    ];

    yield [
      'url' => 'https://youtu.be/vOO-5QGhZgs',
      'is_youtube_url' => TRUE,
      'video_id' => 'vOO-5QGhZgs',
    ];

    yield [
      'url' => 'http://youtu.be',
      'is_youtube_url' => TRUE,
      'video_id' => NULL,
    ];

    yield [
      'url' => 'http://youtu.be/vOO-5QGhZgs',
      'is_youtube_url' => TRUE,
      'video_id' => 'vOO-5QGhZgs',
    ];

    yield [
      'url' => 'youtu.be',
      'is_youtube_url' => TRUE,
      'video_id' => NULL,
    ];

    yield [
      'url' => 'youtu.be/vOO-5QGhZgs',
      'is_youtube_url' => TRUE,
      'video_id' => 'vOO-5QGhZgs',
    ];
  }

}
