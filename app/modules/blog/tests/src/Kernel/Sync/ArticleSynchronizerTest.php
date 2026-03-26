<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Kernel\Sync;

use Drupal\app_blog\Sync\ArticleMapper;
use Drupal\app_blog\Sync\ArticleProcessor;
use Drupal\app_blog\Sync\ArticleSynchronizer;
use Drupal\app_blog\Sync\Domain\SyncContext;
use Drupal\app_blog\Sync\Html\HtmlProcessor;
use Drupal\app_blog\Sync\Parser\ArticleXmlParser;
use Drupal\app_blog\Sync\Utils\EstimatedReadTimeCalculator;
use Drupal\app_contract\Contract\Blog\ArticleRepository;
use Drupal\app_contract\Contract\Media\MediaSynchronizer;
use Drupal\app_contract\Contract\Tag\TagRepository;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\filter\Entity\FilterFormat;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Output\RenderedContent;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(ArticleSynchronizer::class)]
final class ArticleSynchronizerTest extends KernelTestBase {

  use ProphecyTrait;

  // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
  protected static $modules = [
    'app_blog',
    'app_contract',
    'app_file',
    'app_media',
    'app_platform',
    'app_tag',
    'node',
    'media',
    'file',
    'field',
    'image',
    'responsive_image',
    'comment',
    'system',
    'user',
    'taxonomy',
    'text',
    'filter',
    'photoswipe',
    'breakpoint',
    'search_api',
    'twig_tweak',
    'sophron_guesser',
    'sophron',
  ];

  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('taxonomy_term');
    $this->installSchema('node', ['node_access']);
    $this->installConfig(['field', 'node', 'filter']);

    NodeType::create(['type' => 'blog_entry', 'name' => 'Blog entry'])->save();

    FilterFormat::create([
      'format' => 'blog_article',
      'name' => 'Blog Article',
    ])->save();

    $this->createField('body', 'text_with_summary');
    $this->createField('external_id', 'string', ['max_length' => 255]);
    $this->createField('field_content', 'text_long');
    $this->createField('field_source_path_hash', 'string', ['max_length' => 32]);
    $this->createField('field_estimated_read_time', 'integer', ['unsigned' => TRUE]);
    $this->createField('field_tags', 'entity_reference', ['target_type' => 'taxonomy_term'], -1);
    $this->createField('field_media_image', 'entity_reference', ['target_type' => 'media']);
    $this->createField('field_media_attachments', 'entity_reference', ['target_type' => 'media'], -1);
    $this->createField('field_compatibility', 'software_compatibility', [], -1);
  }

  #[DataProvider('sourcePathPrefixProvider')]
  public function testNewArticleCreated(string $source_prefix): void {
    $synchronizer = $this->buildSynchronizer($source_prefix);
    $context = $this->createSyncContext();

    $synchronizer->sync($context);

    $nodes = $this->loadBlogNodes();
    self::assertCount(1, $nodes);
    $node = \reset($nodes);
    self::assertSame('Test Title', $node->getTitle());
    self::assertSame('ext-123', $node->get('external_id')->getString());
  }

  public function testExistingArticleUpdated(): void {
    $existing = Node::create([
      'type' => 'blog_entry',
      'title' => 'Old Title',
      'external_id' => 'ext-123',
      'uid' => 1,
    ]);
    $existing->save();

    $synchronizer = $this->buildSynchronizer();
    $context = $this->createSyncContext(forced: TRUE);

    $synchronizer->sync($context);

    $nodes = $this->loadBlogNodes();
    self::assertCount(1, $nodes);
    $node = \reset($nodes);
    self::assertSame('Test Title', $node->getTitle());
  }

  public function testUnchangedArticleSkipped(): void {
    $existing = Node::create([
      'type' => 'blog_entry',
      'title' => 'Existing Title',
      'external_id' => 'ext-123',
      'uid' => 1,
      'changed' => \strtotime('2030-01-01'),
    ]);
    $existing->save();

    $synchronizer = $this->buildSynchronizer();
    $context = $this->createSyncContext();

    $synchronizer->sync($context);

    $nodes = $this->loadBlogNodes();
    self::assertCount(1, $nodes);
    $node = \reset($nodes);
    self::assertSame('Existing Title', $node->getTitle());
  }

  public function testCompatibilitySynced(): void {
    $synchronizer = $this->buildSynchronizer();
    $context = $this->createSyncContext();

    $synchronizer->sync($context);

    $nodes = $this->loadBlogNodes();
    self::assertCount(1, $nodes);
    $node = \reset($nodes);
    $field = $node->get('field_compatibility');
    self::assertCount(3, $field);
    self::assertSame('drupal', $field->get(0)->get('name')->getValue());
    self::assertSame('^10.3 || ^11', $field->get(0)->get('constraint')->getValue());
    self::assertSame('php', $field->get(1)->get('name')->getValue());
    self::assertSame('^8.3', $field->get(1)->get('constraint')->getValue());
    self::assertSame('docker', $field->get(2)->get('name')->getValue());
    self::assertNull($field->get(2)->get('constraint')->getValue());
  }

  public function testForcedSyncUpdatesEvenUnchanged(): void {
    $existing = Node::create([
      'type' => 'blog_entry',
      'title' => 'Old Title',
      'external_id' => 'ext-123',
      'uid' => 1,
      'changed' => \strtotime('2030-01-01'),
    ]);
    $existing->save();

    $synchronizer = $this->buildSynchronizer();
    $context = $this->createSyncContext(forced: TRUE);

    $synchronizer->sync($context);

    $nodes = $this->loadBlogNodes();
    $node = \reset($nodes);
    self::assertSame('Test Title', $node->getTitle());
  }

  /**
   * @return \Generator<string, array{string}>
   */
  public static function sourcePathPrefixProvider(): \Generator {
    yield 'relative ./ prefix' => ['source_prefix' => './'];
    yield 'bare path' => ['source_prefix' => ''];
  }

  /**
   * @param array<string, mixed> $settings
   */
  private function createField(string $field_name, string $type, array $settings = [], int $cardinality = 1): void {
    FieldStorageConfig::create([
      'field_name' => $field_name,
      'entity_type' => 'node',
      'type' => $type,
      'settings' => $settings,
      'cardinality' => $cardinality,
    ])->save();

    FieldConfig::create([
      'field_name' => $field_name,
      'entity_type' => 'node',
      'bundle' => 'blog_entry',
      'label' => $field_name,
    ])->save();
  }

  private function buildSynchronizer(string $source_prefix = './'): ArticleSynchronizer {
    $schema = \file_get_contents(__DIR__ . '/../../../fixtures/article.xsd');
    \assert($schema !== FALSE);

    vfsStream::setup('content', NULL, [
      'blog' => [
        'article.xsd' => $schema,
        'test' => [
          'article.xml' => $this->buildArticleXml($source_prefix),
          'article.ru.md' => '# Test',
        ],
      ],
    ]);

    $rendered = $this->prophesize(RenderedContent::class);
    $rendered->getContent()->willReturn('<p>Processed content</p>');

    $converter = $this->prophesize(MarkdownConverter::class);
    $converter->convert(Argument::any())->willReturn($rendered->reveal());

    $media_synchronizer = $this->prophesize(MediaSynchronizer::class);
    $media_synchronizer->sync(Argument::any())->willReturn(NULL);

    $article_processor = new ArticleProcessor(
      $converter->reveal(),
      new HtmlProcessor([]),
      $media_synchronizer->reveal(),
      new EstimatedReadTimeCalculator(),
    );

    $tag_repository = $this->prophesize(TagRepository::class);
    $tag_repository->findByExternalId(Argument::any())->willReturn(NULL);

    return new ArticleSynchronizer(
      $this->container->get(ArticleXmlParser::class),
      $article_processor,
      new ArticleMapper(),
      $this->container->get(ArticleRepository::class),
      $tag_repository->reveal(),
      $this->container->get(EntityTypeManagerInterface::class),
    );
  }

  private function createSyncContext(bool $forced = FALSE): SyncContext {
    $context = new SyncContext(
      workingDirectory: vfsStream::url('content/blog'),
      contentRoot: vfsStream::url('content'),
    );
    $context->setForceStatus($forced);

    return $context;
  }

  /**
   * @return array<int, \Drupal\node\Entity\Node>
   */
  private function loadBlogNodes(): array {
    $storage = $this->container->get(EntityTypeManagerInterface::class)->getStorage('node');
    $storage->resetCache();
    $ids = $storage->getQuery()->accessCheck(FALSE)->condition('type', 'blog_entry')->execute();

    return $ids ? $storage->loadMultiple($ids) : [];
  }

  private function buildArticleXml(string $source_prefix = './'): string {
    return <<<XML
    <?xml version="1.0" encoding="UTF-8"?>
    <article xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:noNamespaceSchemaLocation="../article.xsd"
      id="ext-123"
      created="2026-01-01T00:00:00"
      updated="2026-03-01T00:00:00">
      <translations>
        <translation language="ru" primary="true" src="{$source_prefix}article.ru.md">
          <title>Test Title</title>
          <description>Test Description</description>
          <poster src="{$source_prefix}poster.png"/>
        </translation>
      </translations>
      <compatibility>
        <software name="drupal" constraint="^10.3 || ^11"/>
        <software name="php" constraint="^8.3"/>
        <software name="docker"/>
      </compatibility>
    </article>
    XML;
  }

}
