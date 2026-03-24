<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Sync;

use Drupal\app_blog\Sync\ArticleProcessor;
use Drupal\app_blog\Sync\Domain\ArticleTranslation;
use Drupal\app_blog\Sync\Domain\ProcessedArticle;
use Drupal\app_blog\Sync\Html\HtmlProcessor;
use Drupal\app_blog\Sync\Utils\EstimatedReadTimeCalculator;
use Drupal\app_contract\Contract\Media\MediaSynchronizer;
use Drupal\media\MediaInterface;
use Drupal\Tests\UnitTestCase;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Output\RenderedContent;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(ArticleProcessor::class)]
final class ArticleProcessorTest extends UnitTestCase {

  use ProphecyTrait;

  public function testProcessReturnsProcessedArticle(): void {
    $processor = $this->buildProcessor(markdown_html: '<p>Hello</p>');
    $translation = $this->createTranslation();

    $result = $processor->process($translation, vfsStream::url('content'));

    self::assertInstanceOf(ProcessedArticle::class, $result);
    self::assertSame('<p>Hello</p>', $result->html);
    self::assertSame('Test Title', $result->title);
    self::assertSame('Test Description', $result->description);
  }

  public function testSourcePathHashCalculated(): void {
    $processor = $this->buildProcessor();
    $translation = $this->createTranslation();

    $result = $processor->process($translation, vfsStream::url('content'));

    self::assertNotEmpty($result->sourcePathHash);
    self::assertSame(32, \strlen($result->sourcePathHash));
  }

  public function testPosterMediaSynced(): void {
    $poster = $this->prophesize(MediaInterface::class)->reveal();
    $synchronizer = $this->prophesize(MediaSynchronizer::class);
    $synchronizer->sync(vfsStream::url('content/blog/article/poster.png'))
      ->willReturn($poster);
    $synchronizer->sync(Argument::not(vfsStream::url('content/blog/article/poster.png')))
      ->willReturn(NULL);

    $processor = $this->buildProcessor(media_synchronizer: $synchronizer->reveal());
    $translation = $this->createTranslation();

    $result = $processor->process($translation, vfsStream::url('content'));

    self::assertSame($poster, $result->posterMedia);
  }

  public function testPosterNullWhenSyncFails(): void {
    $synchronizer = $this->prophesize(MediaSynchronizer::class);
    $synchronizer->sync(Argument::any())->willReturn(NULL);

    $processor = $this->buildProcessor(media_synchronizer: $synchronizer->reveal());
    $translation = $this->createTranslation();

    $result = $processor->process($translation, vfsStream::url('content'));

    self::assertNull($result->posterMedia);
  }

  public function testAttachmentsMediaSynced(): void {
    $media1 = $this->prophesize(MediaInterface::class)->reveal();
    $media2 = $this->prophesize(MediaInterface::class)->reveal();

    $synchronizer = $this->prophesize(MediaSynchronizer::class);
    $synchronizer->sync(Argument::containingString('poster'))->willReturn(NULL);
    $synchronizer->sync(
      vfsStream::url('content/blog/article/file.pdf'),
      ['title' => 'PDF File'],
    )->willReturn($media1);
    $synchronizer->sync(
      vfsStream::url('content/blog/article/doc.zip'),
      ['title' => 'Archive'],
    )->willReturn($media2);

    $processor = $this->buildProcessor(media_synchronizer: $synchronizer->reveal());
    $translation = $this->createTranslation();
    $translation->addAttachment(['src' => 'file.pdf', 'title' => 'PDF File']);
    $translation->addAttachment(['src' => 'doc.zip', 'title' => 'Archive']);

    $result = $processor->process($translation, vfsStream::url('content'));

    self::assertCount(2, $result->attachmentsMedia);
    self::assertSame($media1, $result->attachmentsMedia[0]);
    self::assertSame($media2, $result->attachmentsMedia[1]);
  }

  public function testFailedAttachmentSkipped(): void {
    $synchronizer = $this->prophesize(MediaSynchronizer::class);
    $synchronizer->sync(Argument::any())->willReturn(NULL);
    $synchronizer->sync(Argument::any(), Argument::any())->willReturn(NULL);

    $processor = $this->buildProcessor(media_synchronizer: $synchronizer->reveal());
    $translation = $this->createTranslation();
    $translation->addAttachment(['src' => 'missing.pdf', 'title' => 'Missing']);

    $result = $processor->process($translation, vfsStream::url('content'));

    self::assertSame([], $result->attachmentsMedia);
  }

  private function buildProcessor(string $markdown_html = '<p>test</p>', ?MediaSynchronizer $media_synchronizer = NULL): ArticleProcessor {
    vfsStream::setup('content', NULL, [
      'blog' => [
        'article' => [
          'index.md' => '# Hello',
        ],
      ],
    ]);

    $rendered = $this->prophesize(RenderedContent::class);
    $rendered->getContent()->willReturn($markdown_html);

    $converter = $this->prophesize(MarkdownConverter::class);
    $converter->convert(Argument::any())->willReturn($rendered->reveal());

    // HtmlProcessor is final — use real instance with no processors.
    $html_processor = new HtmlProcessor([]);

    if (!$media_synchronizer) {
      $synchronizer = $this->prophesize(MediaSynchronizer::class);
      $synchronizer->sync(Argument::any())->willReturn(NULL);
      $media_synchronizer = $synchronizer->reveal();
    }

    return new ArticleProcessor(
      $converter->reveal(),
      $html_processor,
      $media_synchronizer,
      new EstimatedReadTimeCalculator(),
    );
  }

  private function createTranslation(): ArticleTranslation {
    return new ArticleTranslation(
      sourcePath: 'index.md',
      language: 'ru',
      title: 'Test Title',
      description: 'Test Description',
      posterPath: 'poster.png',
      contentDirectory: vfsStream::url('content/blog/article'),
    );
  }

}
