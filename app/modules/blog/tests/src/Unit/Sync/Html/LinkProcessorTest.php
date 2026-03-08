<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Sync\Html;

use Drupal\app_blog\ExternalContent\Domain\ArticleTranslation;
use Drupal\app_blog\Sync\Domain\ArticleProcessingContext;
use Drupal\app_blog\Sync\Html\LinkProcessor;
use Drupal\Component\Utility\Html;
use Drupal\Core\Site\Settings;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LinkProcessor::class)]
final class LinkProcessorTest extends UnitTestCase {

  private LinkProcessor $processor;
  private ArticleProcessingContext $context;
  private vfsStreamDirectory $articleDir;

  public function testExternalLinkNotProcessed(): void {
    $dom = Html::load('<a href="https://example.com">External</a>');

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('href="https://example.com"', $result);
  }

  public function testAnchorLinkNotProcessed(): void {
    $dom = Html::load('<a href="#section">Anchor</a>');

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('href="#section"', $result);
  }

  public function testEmptyHrefNotProcessed(): void {
    $dom = Html::load('<a href="">Empty</a>');

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringNotContainsString('data-source-path-hash', $result);
  }

  public function testRelativeLinkToDirectoryBecomesRepositoryLink(): void {
    vfsStream::newDirectory('linked-article')->at($this->articleDir);

    $dom = Html::load('<a href="linked-article">Link</a>');

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('https://github.com/test/repo/tree/main', $result);
    self::assertStringContainsString('linked-article', $result);
  }

  public function testRelativeLinkToFileBecomesInternalLink(): void {
    vfsStream::newFile('other.md')->at($this->articleDir)->setContent('content');

    $dom = Html::load('<a href="other.md">Other</a>');

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringNotContainsString('href=', $result);
    self::assertStringContainsString('data-source-path-hash=', $result);
  }

  public function testRelativeLinkToNonExistentPathUnchanged(): void {
    $dom = Html::load('<a href="nonexistent">Missing</a>');

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('href="nonexistent"', $result);
  }

  public function testMultipleLinksProcessed(): void {
    vfsStream::newFile('target.md')->at($this->articleDir)->setContent('content');

    $html = <<<'HTML'
    <a href="https://ext.com">Ext</a>
    <a href="target.md">Internal</a>
    <a href="#anchor">Anchor</a>
    HTML;
    $dom = Html::load($html);

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('href="https://ext.com"', $result);
    self::assertStringContainsString('data-source-path-hash=', $result);
    self::assertStringContainsString('href="#anchor"', $result);
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    $root = vfsStream::setup('content', NULL, [
      'blog' => [
        'article' => [],
      ],
    ]);

    $article_dir = $root->getChild('blog/article');
    \assert($article_dir instanceof vfsStreamDirectory);
    $this->articleDir = $article_dir;

    $content_url = vfsStream::url('content');

    new Settings([
      'external_content_directory' => $content_url,
      'external_content_repository_url' => 'https://github.com/test/repo',
    ]);

    $this->processor = new LinkProcessor();
    $translation = new ArticleTranslation(
      sourcePath: 'article.ru.md',
      language: 'ru',
      title: 'Test',
      description: 'Test',
      posterPath: 'poster.png',
      contentDirectory: vfsStream::url('content/blog/article'),
    );
    $this->context = new ArticleProcessingContext($translation, $content_url);
  }

}
