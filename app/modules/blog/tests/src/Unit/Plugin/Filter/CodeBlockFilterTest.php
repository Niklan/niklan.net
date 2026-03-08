<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Plugin\Filter;

use Drupal\app_blog\Plugin\Filter\CodeBlockFilter;
use Drupal\Core\Render\RendererInterface;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Argument;

#[CoversClass(CodeBlockFilter::class)]
final class CodeBlockFilterTest extends UnitTestCase {

  use ProphecyTrait;

  public function testTextWithoutCodeBlockPassedThrough(): void {
    $renderer = $this->prophesize(RendererInterface::class);
    $filter = $this->createFilter($renderer->reveal());

    $text = '<p>No code blocks</p>';
    $result = $filter->process($text, 'en');

    self::assertSame($text, $result->getProcessedText());
    $renderer->renderInIsolation(Argument::any())->shouldNotHaveBeenCalled();
  }

  public function testCodeBlockPlaceholderReplacedWithRendered(): void {
    $renderer = $this->prophesize(RendererInterface::class);
    $matches_code_block = static fn (array $build): bool => $build['#component'] === 'app_blog:code-block'
      && $build['#props']['language'] === 'php'
      && $build['#props']['code'] === 'echo 1;';
    $renderer->renderInIsolation(Argument::that($matches_code_block))
      ->willReturn('<pre class="hljs"><code>echo 1;</code></pre>');

    $filter = $this->createFilter($renderer->reveal());
    $text = '<app-code-block data-language="php">echo 1;</app-code-block>';

    $result = $filter->process($text, 'en');

    self::assertStringContainsString('class="hljs"', $result->getProcessedText());
    self::assertStringNotContainsString('<app-code-block', $result->getProcessedText());
  }

  public function testCodeBlockPropsExtracted(): void {
    $captured_build = NULL;
    $renderer = $this->prophesize(RendererInterface::class);
    $renderer->renderInIsolation(Argument::any())
      ->will(static function (array $args) use (&$captured_build): string {
        $captured_build = $args[0];
        return '<pre>code</pre>';
      });

    $filter = $this->createFilter($renderer->reveal());
    $text = '<app-code-block data-language="js" data-highlighted-lines="1-3" data-header="app.js">const x = 1;</app-code-block>';

    $filter->process($text, 'en');

    self::assertNotNull($captured_build);
    self::assertSame('js', $captured_build['#props']['language']);
    self::assertSame('1-3', $captured_build['#props']['highlighted_lines']);
    self::assertSame('app.js', $captured_build['#props']['heading']);
    self::assertSame('const x = 1;', $captured_build['#props']['code']);
  }

  public function testMissingOptionalPropsAreNull(): void {
    $captured_build = NULL;
    $renderer = $this->prophesize(RendererInterface::class);
    $renderer->renderInIsolation(Argument::any())
      ->will(static function (array $args) use (&$captured_build): string {
        $captured_build = $args[0];
        return '<pre>code</pre>';
      });

    $filter = $this->createFilter($renderer->reveal());
    $text = '<app-code-block>plain code</app-code-block>';

    $filter->process($text, 'en');

    self::assertNotNull($captured_build);
    self::assertNull($captured_build['#props']['language']);
    self::assertNull($captured_build['#props']['highlighted_lines']);
    self::assertNull($captured_build['#props']['heading']);
  }

  public function testMultipleCodeBlocks(): void {
    $call_count = 0;
    $renderer = $this->prophesize(RendererInterface::class);
    $renderer->renderInIsolation(Argument::any())
      ->will(static function () use (&$call_count): string {
        $call_count++;
        return '<pre>code</pre>';
      });

    $filter = $this->createFilter($renderer->reveal());
    $text = <<<'HTML'
    <app-code-block data-language="php">echo 1;</app-code-block>
    <p>text</p>
    <app-code-block data-language="js">let x;</app-code-block>
    HTML;

    $filter->process($text, 'en');

    self::assertSame(2, $call_count);
  }

  private function createFilter(RendererInterface $renderer): CodeBlockFilter {
    return new CodeBlockFilter(
      [], 'app_blog_code_block', ['provider' => 'app_blog'],
      $renderer,
    );
  }

}
