<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Plugin\Filter;

use Drupal\app_blog\Plugin\Filter\MediaFilter;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;
use Drupal\media\MediaSourceInterface;
use Drupal\Tests\app_blog\Unit\Plugin\Filter\Stub\StubRenderer;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(MediaFilter::class)]
final class MediaFilterTest extends UnitTestCase {

  use ProphecyTrait;

  public function testTextWithoutMediaPlaceholderPassedThrough(): void {
    $filter = $this->createFilter();

    $text = '<p>No media</p>';
    $result = $filter->process($text, 'en');

    self::assertSame($text, $result->getProcessedText());
  }

  public function testUnknownBundleRemovesElement(): void {
    $media = $this->prophesize(MediaInterface::class);
    $media->uuid()->willReturn('uuid-1');
    $media->getCacheTags()->willReturn(['media:1']);

    $filter = $this->createFilter(mediaByUuid: ['uuid-1' => $media->reveal()]);
    $text = '<app-media data-uuid="uuid-1" data-bundle="unknown"></app-media>';

    $result = $filter->process($text, 'en');

    self::assertStringNotContainsString('<app-media', $result->getProcessedText());
    self::assertContains('media:1', $result->getCacheTags());
  }

  public function testMissingMediaRemovesElement(): void {
    $filter = $this->createFilter();
    $text = '<app-media data-uuid="missing-uuid" data-bundle="image"></app-media>';

    $result = $filter->process($text, 'en');

    self::assertStringNotContainsString('<app-media', $result->getProcessedText());
  }

  public function testRemoteVideoRenderedViaViewBuilder(): void {
    $media = $this->prophesize(MediaInterface::class);
    $media->uuid()->willReturn('yt-uuid');
    $media->getCacheTags()->willReturn(['media:5']);

    $view_builder = $this->prophesize(EntityViewBuilderInterface::class);
    $view_builder->view($media->reveal())->willReturn(['#markup' => '<iframe src="youtube"></iframe>']);

    $filter = $this->createFilter(
      mediaByUuid: ['yt-uuid' => $media->reveal()],
      viewBuilder: $view_builder->reveal(),
      renderedOutput: '<iframe src="youtube"></iframe>',
    );
    $text = '<app-media data-uuid="yt-uuid" data-bundle="remote_video"></app-media>';

    $result = $filter->process($text, 'en');

    self::assertStringContainsString('youtube', $result->getProcessedText());
    self::assertContains('media:5', $result->getCacheTags());
  }

  public function testCacheTagsCollected(): void {
    $media1 = $this->prophesize(MediaInterface::class);
    $media1->uuid()->willReturn('uuid-1');
    $media1->getCacheTags()->willReturn(['media:1']);

    $media2 = $this->prophesize(MediaInterface::class);
    $media2->uuid()->willReturn('uuid-2');
    $media2->getCacheTags()->willReturn(['media:2']);

    $filter = $this->createFilter(
      mediaByUuid: [
        'uuid-1' => $media1->reveal(),
        'uuid-2' => $media2->reveal(),
      ],
    );

    $text = <<<'HTML'
    <app-media data-uuid="uuid-1" data-bundle="unknown"></app-media>
    <app-media data-uuid="uuid-2" data-bundle="unknown"></app-media>
    HTML;

    $result = $filter->process($text, 'en');

    self::assertContains('media:1', $result->getCacheTags());
    self::assertContains('media:2', $result->getCacheTags());
  }

  public function testAttachmentsFromRenderedImagePropagated(): void {
    $media = $this->createImageMedia('img-uuid', 'public://image.jpg', ['media:10']);
    $renderer = new StubRenderer('<span class="lightbox"><img /></span>', ['library' => ['photoswipe/photoswipe']]);

    $filter = $this->createFilter(
      mediaByUuid: ['img-uuid' => $media],
      renderer: $renderer,
    );
    $text = '<app-media data-uuid="img-uuid" data-bundle="image" data-alt="Test"></app-media>';

    $result = $filter->process($text, 'en');

    self::assertContains('photoswipe/photoswipe', $result->getAttachments()['library']);
    self::assertContains('media:10', $result->getCacheTags());
  }

  public function testAttachmentsMergedFromMultipleMedia(): void {
    $media1 = $this->createImageMedia('uuid-1', 'public://img1.jpg', ['media:1']);
    $media2 = $this->createImageMedia('uuid-2', 'public://img2.jpg', ['media:2']);
    $renderer = new StubRenderer('<span class="lightbox"><img /></span>');

    $filter = $this->createFilter(
      mediaByUuid: [
        'uuid-1' => $media1,
        'uuid-2' => $media2,
      ],
      renderer: $renderer,
    );

    $text = <<<'HTML'
    <app-media data-uuid="uuid-1" data-bundle="image" data-alt="A"></app-media>
    <app-media data-uuid="uuid-2" data-bundle="image" data-alt="B"></app-media>
    HTML;

    $result = $filter->process($text, 'en');

    self::assertContains('test/lib-1', $result->getAttachments()['library']);
    self::assertContains('test/lib-2', $result->getAttachments()['library']);
  }

  private function createFilter(array $media_by_uuid = [], ?EntityViewBuilderInterface $view_builder = NULL, string $rendered_output = '', ?RendererInterface $renderer = NULL): MediaFilter {
    $query = $this->prophesize(QueryInterface::class);
    $query->accessCheck(FALSE)->willReturn($query->reveal());
    $query->condition(Argument::cetera())->willReturn($query->reveal());
    $query->execute()->willReturn($media_by_uuid ? \array_combine(\range(1, \count($media_by_uuid)), \array_keys($media_by_uuid)) : []);

    $storage = $this->prophesize(EntityStorageInterface::class);
    $storage->getQuery()->willReturn($query->reveal());
    $storage->loadMultiple(Argument::any())->willReturn($media_by_uuid);

    $entity_type_manager = $this->prophesize(EntityTypeManagerInterface::class);
    $entity_type_manager->getStorage('media')->willReturn($storage->reveal());

    if ($view_builder) {
      $entity_type_manager->getViewBuilder('media')->willReturn($view_builder);
    }

    if (!$renderer) {
      $renderer_prophecy = $this->prophesize(RendererInterface::class);
      $renderer_prophecy->renderInIsolation(Argument::any())->willReturn($rendered_output);
      $renderer = $renderer_prophecy->reveal();
    }

    return new MediaFilter(
      [], 'app_blog_media', ['provider' => 'app_blog'],
      $entity_type_manager->reveal(),
      $renderer,
    );
  }

  /**
   * @param list<string> $cache_tags
   */
  private function createImageMedia(string $uuid, string $file_uri, array $cache_tags = []): MediaInterface {
    $file = $this->prophesize(FileInterface::class);
    $file->getFileUri()->willReturn($file_uri);

    $source = $this->prophesize(MediaSourceInterface::class);
    $source->getConfiguration()->willReturn(['source_field' => 'field_media_image']);

    $field_value = new class ($file->reveal()) {

      public function __construct(private readonly FileInterface $file) {}

      public function first(): object {
        $file = $this->file;
        return new class ($file) {

          public function __construct(private readonly FileInterface $file) {}

          public function get(string $property): object {
            $file = $this->file;
            return new class ($file) {

              public function __construct(private readonly FileInterface $file) {}

              public function getValue(): FileInterface {
                return $this->file;
              }

            };
          }

        };
      }

    };

    $media = $this->prophesize(MediaInterface::class);
    $media->uuid()->willReturn($uuid);
    $media->getCacheTags()->willReturn($cache_tags);
    $media->getSource()->willReturn($source->reveal());
    $media->get('field_media_image')->willReturn($field_value);

    return $media->reveal();
  }

}
