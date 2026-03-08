<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Sync\Html;

use Drupal\app_blog\ExternalContent\Domain\ArticleTranslation;
use Drupal\app_blog\Sync\Domain\ArticleProcessingContext;
use Drupal\app_blog\Sync\Html\CodeBlockProcessor;
use Drupal\Component\Utility\Html;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CodeBlockProcessor::class)]
final class CodeBlockProcessorTest extends UnitTestCase {

  private CodeBlockProcessor $processor;
  private ArticleProcessingContext $context;

  public function testPreWithCodeReplacedByPlaceholder(): void {
    $dom = Html::load('<pre data-language="php"><code>echo "hello";</code></pre>');

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('<app-code-block', $result);
    self::assertStringContainsString('data-language="php"', $result);
    self::assertStringContainsString('echo "hello";', $result);
    self::assertStringNotContainsString('<pre', $result);
  }

  public function testPreWithoutCodeIsNotProcessed(): void {
    $dom = Html::load('<pre>plain text</pre>');

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('<pre>', $result);
    self::assertStringNotContainsString('<app-code-block', $result);
  }

  public function testLanguageFromCodeClass(): void {
    $dom = Html::load('<pre><code class="language-javascript">const x = 1;</code></pre>');

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('data-language="javascript"', $result);
  }

  public function testInfoAttributesParsed(): void {
    $info = '{"highlighted_lines":"1-3","header":"example.php"}';
    $dom = Html::load('<pre data-language="php" data-info=\'' . $info . '\'><code>$x = 1;</code></pre>');

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('data-highlighted-lines="1-3"', $result);
    self::assertStringContainsString('data-header="example.php"', $result);
  }

  public function testMultipleCodeBlocks(): void {
    $dom = Html::load(<<<'HTML'
    <pre data-language="php"><code>echo 1;</code></pre>
    <p>text</p>
    <pre data-language="js"><code>let x;</code></pre>
    HTML);

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertSame(2, \substr_count($result, '<app-code-block'));
    self::assertStringContainsString('<p>text</p>', $result);
  }

  public function testNoCodeBlocksInHtml(): void {
    $dom = Html::load('<p>No code here</p>');

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('<p>No code here</p>', $result);
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();
    $this->processor = new CodeBlockProcessor();
    $translation = new ArticleTranslation(
      sourcePath: 'article.ru.md',
      language: 'ru',
      title: 'Test',
      description: 'Test',
      posterPath: 'poster.png',
      contentDirectory: '/tmp/test',
    );
    $this->context = new ArticleProcessingContext($translation, '/tmp');
  }

}
