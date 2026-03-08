<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Sync\Html;

use Drupal\app_blog\Sync\Domain\ArticleTranslation;
use Drupal\app_blog\Sync\Domain\ArticleProcessingContext;
use Drupal\app_blog\Sync\Html\CalloutProcessor;
use Drupal\Component\Utility\Html;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(CalloutProcessor::class)]
final class CalloutProcessorTest extends UnitTestCase {

  private CalloutProcessor $processor;
  private ArticleProcessingContext $context;

  #[DataProvider('calloutTypesProvider')]
  public function testCalloutTypeConverted(string $type): void {
    $html = <<<HTML
    <div data-selector="niklan:container-directive" data-type="{$type}">
      <div data-selector="content"><p>Body</p></div>
    </div>
    HTML;
    $dom = Html::load($html);

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('<app-callout', $result);
    self::assertStringContainsString('data-type="' . $type . '"', $result);
    self::assertStringContainsString('<app-callout-body>', $result);
  }

  public function testCalloutWithTitle(): void {
    $html = <<<'HTML'
    <div data-selector="niklan:container-directive" data-type="note">
      <div data-selector="inline-content">Custom title</div>
      <div data-selector="content"><p>Body text</p></div>
    </div>
    HTML;
    $dom = Html::load($html);

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('<app-callout-title>Custom title</app-callout-title>', $result);
    self::assertStringContainsString('<app-callout-body>', $result);
  }

  public function testNonCalloutDirectiveIgnored(): void {
    $html = <<<'HTML'
    <div data-selector="niklan:container-directive" data-type="figure">
      <div data-selector="content"><p>Figure</p></div>
    </div>
    HTML;
    $dom = Html::load($html);

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringNotContainsString('<app-callout', $result);
    self::assertStringContainsString('data-type="figure"', $result);
  }

  public function testUnknownDirectiveTypeIgnored(): void {
    $html = <<<'HTML'
    <div data-selector="niklan:container-directive" data-type="custom">
      <div data-selector="content"><p>Custom</p></div>
    </div>
    HTML;
    $dom = Html::load($html);

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringNotContainsString('<app-callout', $result);
  }

  public function testMultipleCallouts(): void {
    $html = <<<'HTML'
    <div data-selector="niklan:container-directive" data-type="note">
      <div data-selector="content"><p>Note</p></div>
    </div>
    <div data-selector="niklan:container-directive" data-type="warning">
      <div data-selector="content"><p>Warning</p></div>
    </div>
    HTML;
    $dom = Html::load($html);

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertSame(2, \substr_count($result, '<app-callout '));
  }

  public static function calloutTypesProvider(): \Generator {
    yield 'note' => ['note'];
    yield 'tip' => ['tip'];
    yield 'important' => ['important'];
    yield 'warning' => ['warning'];
    yield 'caution' => ['caution'];
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();
    $this->processor = new CalloutProcessor();
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
