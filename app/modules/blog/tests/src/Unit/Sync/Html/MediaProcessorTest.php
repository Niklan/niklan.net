<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Sync\Html;

use Drupal\app_blog\ExternalContent\Domain\ArticleTranslation;
use Drupal\app_blog\Sync\Domain\ArticleProcessingContext;
use Drupal\app_blog\Sync\Html\MediaProcessor;
use Drupal\app_contract\Contract\Media\MediaSynchronizer;
use Drupal\Component\Utility\Html;
use Drupal\media\MediaInterface;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(MediaProcessor::class)]
final class MediaProcessorTest extends UnitTestCase {

  use ProphecyTrait;

  private ArticleProcessingContext $context;

  public function testImageReplacedWithPlaceholder(): void {
    $media = $this->prophesize(MediaInterface::class);
    $media->uuid()->willReturn('test-uuid-123');
    $synchronizer = $this->prophesize(MediaSynchronizer::class);
    $synchronizer->sync('/content/blog/2026/article/image.png')->willReturn($media->reveal());

    $processor = new MediaProcessor($synchronizer->reveal());
    $dom = Html::load('<img src="image.png" alt="Description">');

    $processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('<app-media', $result);
    self::assertStringContainsString('data-uuid="test-uuid-123"', $result);
    self::assertStringContainsString('data-bundle="image"', $result);
    self::assertStringContainsString('data-alt="Description"', $result);
    self::assertStringNotContainsString('<img', $result);
  }

  public function testImageRemovedWhenSyncFails(): void {
    $synchronizer = $this->prophesize(MediaSynchronizer::class);
    $synchronizer->sync('/content/blog/2026/article/missing.png')->willReturn(NULL);

    $processor = new MediaProcessor($synchronizer->reveal());
    $dom = Html::load('<p><img src="missing.png" alt="Missing"></p>');

    $processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringNotContainsString('<img', $result);
    self::assertStringNotContainsString('<app-media', $result);
  }

  public function testVideoDirectiveReplacedWithPlaceholder(): void {
    $media = $this->prophesize(MediaInterface::class);
    $media->uuid()->willReturn('video-uuid');
    $synchronizer = $this->prophesize(MediaSynchronizer::class);
    $synchronizer->sync('/content/blog/2026/article/video/demo.mp4')->willReturn($media->reveal());

    $processor = new MediaProcessor($synchronizer->reveal());
    $html = <<<'HTML'
    <div data-selector="niklan:leaf-directive" data-type="video" data-argument="video/demo.mp4" muted="" autoplay="" loop="">
      <div data-selector="inline-content">Demo video</div>
    </div>
    HTML;
    $dom = Html::load($html);

    $processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('<app-media', $result);
    self::assertStringContainsString('data-uuid="video-uuid"', $result);
    self::assertStringContainsString('data-bundle="video"', $result);
    self::assertStringContainsString('data-title="Demo video"', $result);
    self::assertStringContainsString(' muted', $result);
    self::assertStringContainsString(' autoplay', $result);
    self::assertStringContainsString(' loop', $result);
  }

  public function testVideoDirectiveWithoutSrcRemoved(): void {
    $synchronizer = $this->prophesize(MediaSynchronizer::class);

    $processor = new MediaProcessor($synchronizer->reveal());
    $html = '<div data-selector="niklan:leaf-directive" data-type="video"></div>';
    $dom = Html::load($html);

    $processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringNotContainsString('leaf-directive', $result);
  }

  public function testYouTubeDirectiveReplacedWithPlaceholder(): void {
    $media = $this->prophesize(MediaInterface::class);
    $media->uuid()->willReturn('yt-uuid');
    $synchronizer = $this->prophesize(MediaSynchronizer::class);
    $synchronizer->sync('https://youtu.be/dQw4w9WgXcQ')
      ->willReturn($media->reveal());

    $processor = new MediaProcessor($synchronizer->reveal());
    $html = '<div data-selector="niklan:leaf-directive" data-type="youtube" vid="dQw4w9WgXcQ"></div>';
    $dom = Html::load($html);

    $processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('<app-media', $result);
    self::assertStringContainsString('data-uuid="yt-uuid"', $result);
    self::assertStringContainsString('data-bundle="remote_video"', $result);
  }

  public function testYouTubeDirectiveWithoutVidRemoved(): void {
    $synchronizer = $this->prophesize(MediaSynchronizer::class);

    $processor = new MediaProcessor($synchronizer->reveal());
    $html = '<div data-selector="niklan:leaf-directive" data-type="youtube"></div>';
    $dom = Html::load($html);

    $processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringNotContainsString('leaf-directive', $result);
  }

  public function testVideoControlsAttribute(): void {
    $media = $this->prophesize(MediaInterface::class);
    $media->uuid()->willReturn('video-uuid');
    $synchronizer = $this->prophesize(MediaSynchronizer::class);
    $synchronizer->sync('/content/blog/2026/article/video.mp4')
      ->willReturn($media->reveal());

    $processor = new MediaProcessor($synchronizer->reveal());
    $html = <<<'HTML'
    <div data-selector="niklan:leaf-directive" data-type="video" data-argument="video.mp4" controls="">
      <div data-selector="inline-content">Title</div>
    </div>
    HTML;
    $dom = Html::load($html);

    $processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString(' controls', $result);
    self::assertStringNotContainsString('autoplay', $result);
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();
    $translation = new ArticleTranslation(
      sourcePath: 'article.ru.md',
      language: 'ru',
      title: 'Test',
      description: 'Test',
      posterPath: 'poster.png',
      contentDirectory: '/content/blog/2026/article',
    );
    $this->context = new ArticleProcessingContext($translation, '/content');
  }

}
