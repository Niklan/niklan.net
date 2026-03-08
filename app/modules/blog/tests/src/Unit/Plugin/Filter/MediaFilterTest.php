<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Plugin\Filter;

use Drupal\app_blog\Plugin\Filter\MediaFilter;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\media\MediaInterface;
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

  /**
   * @param array<string, \Drupal\media\MediaInterface> $mediaByUuid
   */
  private function createFilter(array $mediaByUuid = [], ?EntityViewBuilderInterface $viewBuilder = NULL, string $renderedOutput = ''): MediaFilter {
    $query = $this->prophesize(QueryInterface::class);
    $query->accessCheck(FALSE)->willReturn($query->reveal());
    $query->condition(Argument::cetera())->willReturn($query->reveal());
    $query->execute()->willReturn($mediaByUuid ? \array_combine(\range(1, \count($mediaByUuid)), \array_keys($mediaByUuid)) : []);

    $storage = $this->prophesize(EntityStorageInterface::class);
    $storage->getQuery()->willReturn($query->reveal());
    $storage->loadMultiple(Argument::any())->willReturn($mediaByUuid);

    $entity_type_manager = $this->prophesize(EntityTypeManagerInterface::class);
    $entity_type_manager->getStorage('media')->willReturn($storage->reveal());

    if ($viewBuilder) {
      $entity_type_manager->getViewBuilder('media')->willReturn($viewBuilder);
    }

    $renderer = $this->prophesize(RendererInterface::class);
    $renderer->renderInIsolation(Argument::any())->willReturn($renderedOutput);

    return new MediaFilter(
      [], 'app_blog_media', ['provider' => 'app_blog'],
      $entity_type_manager->reveal(),
      $renderer->reveal(),
    );
  }

}
