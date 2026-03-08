<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Kernel\Plugin\Filter;

use Drupal\Core\Database\Connection;
use Drupal\app_blog\Plugin\Filter\InternalLinkFilter;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InternalLinkFilter::class)]
final class InternalLinkFilterTest extends KernelTestBase {

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
  private InternalLinkFilter $filter;

  public function testTextWithoutHashAttributePassedThrough(): void {
    $text = '<p>No links</p>';
    $result = $this->filter->process($text, 'en');

    self::assertSame($text, $result->getProcessedText());
  }

  public function testLinkResolvedToNodeUrl(): void {
    $node = $this->createBlogNode('test-hash-123');
    $text = '<a data-source-path-hash="test-hash-123">Link</a>';

    $result = $this->filter->process($text, 'en');

    self::assertStringContainsString('href="/node/' . $node->id() . '"', $result->getProcessedText());
    self::assertStringNotContainsString('data-source-path-hash', $result->getProcessedText());
  }

  public function testCacheTagsAddedForResolvedLink(): void {
    $node = $this->createBlogNode('hash-with-cache');
    $text = '<a data-source-path-hash="hash-with-cache">Link</a>';

    $result = $this->filter->process($text, 'en');

    self::assertContains('node:' . $node->id(), $result->getCacheTags());
  }

  public function testUnresolvedHashRemovesAttribute(): void {
    $text = '<a data-source-path-hash="nonexistent-hash">Link</a>';

    $result = $this->filter->process($text, 'en');

    self::assertStringNotContainsString('data-source-path-hash', $result->getProcessedText());
    self::assertStringNotContainsString('href=', $result->getProcessedText());
  }

  public function testMultipleLinksResolved(): void {
    $node1 = $this->createBlogNode('hash-aaa');
    $node2 = $this->createBlogNode('hash-bbb');

    $text = <<<'HTML'
    <a data-source-path-hash="hash-aaa">First</a>
    <p>separator</p>
    <a data-source-path-hash="hash-bbb">Second</a>
    HTML;

    $result = $this->filter->process($text, 'en');

    self::assertStringContainsString('/node/' . $node1->id(), $result->getProcessedText());
    self::assertStringContainsString('/node/' . $node2->id(), $result->getProcessedText());
    self::assertContains('node:' . $node1->id(), $result->getCacheTags());
    self::assertContains('node:' . $node2->id(), $result->getCacheTags());
  }

  public function testMixedResolvedAndUnresolvedLinks(): void {
    $node = $this->createBlogNode('known-hash');

    $text = <<<'HTML'
    <a data-source-path-hash="known-hash">Known</a>
    <a data-source-path-hash="unknown-hash">Unknown</a>
    HTML;

    $result = $this->filter->process($text, 'en');

    self::assertStringContainsString('/node/' . $node->id(), $result->getProcessedText());
    self::assertStringNotContainsString('data-source-path-hash', $result->getProcessedText());
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('node');
    $this->installEntitySchema('user');

    NodeType::create(['type' => 'blog_entry', 'name' => 'Blog entry'])->save();

    FieldStorageConfig::create([
      'field_name' => 'field_source_path_hash',
      'entity_type' => 'node',
      'type' => 'string',
      'settings' => ['max_length' => 32],
    ])->save();

    FieldConfig::create([
      'field_name' => 'field_source_path_hash',
      'entity_type' => 'node',
      'bundle' => 'blog_entry',
      'label' => 'Source path hash',
    ])->save();

    $database = $this->container->get('database');
    \assert($database instanceof Connection);
    $this->filter = new InternalLinkFilter([], InternalLinkFilter::ID, ['provider' => 'app_blog'], $database);
  }

  private function createBlogNode(string $source_path_hash): Node {
    $node = Node::create([
      'type' => 'blog_entry',
      'title' => 'Test article',
      'field_source_path_hash' => $source_path_hash,
    ]);
    $node->save();

    return $node;
  }

}
