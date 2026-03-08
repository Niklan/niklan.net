<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Sync\Html;

use Drupal\app_blog\Sync\Domain\ArticleTranslation;
use Drupal\app_blog\Sync\Domain\ArticleProcessingContext;
use Drupal\app_blog\Sync\Html\FigureProcessor;
use Drupal\Component\Utility\Html;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FigureProcessor::class)]
final class FigureProcessorTest extends UnitTestCase {

  private FigureProcessor $processor;
  private ArticleProcessingContext $context;

  public function testFigureDirectiveConverted(): void {
    $html = <<<'HTML'
    <div data-selector="niklan:container-directive" data-type="figure">
      <div data-selector="content"><img src="test.png" alt="Test"></div>
    </div>
    HTML;
    $dom = Html::load($html);

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('<figure>', $result);
    self::assertStringContainsString('<img', $result);
    self::assertStringNotContainsString('data-selector="niklan:container-directive"', $result);
  }

  public function testFigureWithFigcaption(): void {
    $html = <<<'HTML'
    <div data-selector="niklan:container-directive" data-type="figure">
      <div data-selector="content">
        <img src="test.png" alt="Test">
        <div data-selector="niklan:container-directive" data-type="figcaption">
          <div data-selector="content"><p>Caption text</p></div>
        </div>
      </div>
    </div>
    HTML;
    $dom = Html::load($html);

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('<figure>', $result);
    self::assertStringContainsString('<figcaption>', $result);
    self::assertStringContainsString('Caption text', $result);
  }

  public function testNonFigureDirectiveIgnored(): void {
    $html = <<<'HTML'
    <div data-selector="niklan:container-directive" data-type="note">
      <div data-selector="content"><p>Note</p></div>
    </div>
    HTML;
    $dom = Html::load($html);

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('data-type="note"', $result);
  }

  public function testNoDirectivesInHtml(): void {
    $dom = Html::load('<p>Plain text</p>');

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertStringContainsString('<p>Plain text</p>', $result);
  }

  public function testMultipleFigures(): void {
    $html = <<<'HTML'
    <div data-selector="niklan:container-directive" data-type="figure">
      <div data-selector="content"><img src="a.png"></div>
    </div>
    <p>separator</p>
    <div data-selector="niklan:container-directive" data-type="figure">
      <div data-selector="content"><img src="b.png"></div>
    </div>
    HTML;
    $dom = Html::load($html);

    $this->processor->process($dom, $this->context);

    $result = Html::serialize($dom);
    self::assertSame(2, \substr_count($result, '<figure>'));
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();
    $this->processor = new FigureProcessor();
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
