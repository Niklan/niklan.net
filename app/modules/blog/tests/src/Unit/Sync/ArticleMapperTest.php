<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Sync;

use Prophecy\Prophecy\ObjectProphecy;
use Drupal\app_blog\Sync\ArticleMapper;
use Drupal\app_blog\Sync\Domain\ProcessedArticle;
use Drupal\app_contract\Contract\Node\Article;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\media\MediaInterface;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(ArticleMapper::class)]
final class ArticleMapperTest extends UnitTestCase {

  use ProphecyTrait;

  private ArticleMapper $mapper;

  public function testTitleMapped(): void {
    $entity = $this->createArticleMock();
    $processed = $this->createProcessed(title: 'My Article');

    $this->mapper->toEntity($processed, $entity->reveal());

    $entity->setTitle('My Article')->shouldHaveBeenCalledOnce();
  }

  public function testDescriptionMappedToBody(): void {
    $entity = $this->createArticleMock();
    $processed = $this->createProcessed(description: 'Article summary');

    $this->mapper->toEntity($processed, $entity->reveal());

    $entity->set('body', 'Article summary')->shouldHaveBeenCalledOnce();
  }

  public function testPosterMapped(): void {
    $poster = $this->prophesize(MediaInterface::class)->reveal();
    $entity = $this->createArticleMock();
    $processed = $this->createProcessed(posterMedia: $poster);

    $this->mapper->toEntity($processed, $entity->reveal());

    $entity->set('field_media_image', $poster)->shouldHaveBeenCalledOnce();
  }

  public function testContentFieldMapped(): void {
    $entity = $this->createArticleMock();
    $processed = $this->createProcessed(html: '<p>Content</p>');

    $this->mapper->toEntity($processed, $entity->reveal());

    $entity->set('field_content', [
      'value' => '<p>Content</p>',
      'format' => 'blog_article',
    ])->shouldHaveBeenCalledOnce();
  }

  public function testSourcePathHashMapped(): void {
    $entity = $this->createArticleMock();
    $processed = $this->createProcessed(sourcePathHash: 'abc123');

    $this->mapper->toEntity($processed, $entity->reveal());

    $entity->set('field_source_path_hash', 'abc123')->shouldHaveBeenCalledOnce();
  }

  public function testEstimatedReadTimeMapped(): void {
    $entity = $this->createArticleMock();
    $processed = $this->createProcessed(estimatedReadTime: 5);

    $this->mapper->toEntity($processed, $entity->reveal());

    $entity->set('field_estimated_read_time', 5)->shouldHaveBeenCalledOnce();
  }

  public function testAttachmentsMapped(): void {
    $media1 = $this->prophesize(MediaInterface::class)->reveal();
    $media2 = $this->prophesize(MediaInterface::class)->reveal();

    $field_list = $this->prophesize(FieldItemListInterface::class);
    $field_list->appendItem($media1)->shouldBeCalledOnce();
    $field_list->appendItem($media2)->shouldBeCalledOnce();

    $entity = $this->createArticleMock();
    $entity->get('field_media_attachments')->willReturn($field_list->reveal());

    $processed = $this->createProcessed(attachmentsMedia: [$media1, $media2]);

    $this->mapper->toEntity($processed, $entity->reveal());

    $entity->set('field_media_attachments', NULL)->shouldHaveBeenCalledOnce();
  }

  public function testEmptyAttachmentsClearsField(): void {
    $entity = $this->createArticleMock();
    $processed = $this->createProcessed();

    $this->mapper->toEntity($processed, $entity->reveal());

    $entity->set('field_media_attachments', NULL)->shouldHaveBeenCalledOnce();
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();
    $this->mapper = new ArticleMapper();
  }

  /**
   * @return \Prophecy\Prophecy\ObjectProphecy<\Drupal\app_contract\Contract\Node\Article>
   */
  private function createArticleMock(): ObjectProphecy {
    $entity = $this->prophesize(Article::class);
    $entity->setTitle(Argument::any())->willReturn($entity->reveal());
    $entity->set(Argument::cetera())->willReturn($entity->reveal());

    $field_list = $this->prophesize(FieldItemListInterface::class);
    $entity->get('field_media_attachments')->willReturn($field_list->reveal());

    return $entity;
  }

  /**
   * @param list<\Drupal\media\MediaInterface> $attachmentsMedia
   */
  private function createProcessed(string $html = '<p>test</p>', string $sourcePathHash = 'hash', int $estimatedReadTime = 1, string $title = 'Title', string $description = 'Desc', ?MediaInterface $posterMedia = NULL, array $attachmentsMedia = []): ProcessedArticle {
    return new ProcessedArticle(
      html: $html,
      sourcePathHash: $sourcePathHash,
      estimatedReadTime: $estimatedReadTime,
      title: $title,
      description: $description,
      posterMedia: $posterMedia,
      attachmentsMedia: $attachmentsMedia,
    );
  }

}
