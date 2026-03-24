<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Sync\Html;

use Drupal\app_blog\Sync\Contract\HtmlContentProcessor;
use Drupal\app_blog\Sync\Domain\ArticleProcessingContext;
use Drupal\app_blog\Sync\Domain\ArticleTranslation;
use Drupal\app_blog\Sync\Html\HtmlProcessor;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(HtmlProcessor::class)]
final class HtmlProcessorTest extends UnitTestCase {

  use ProphecyTrait;

  private ArticleProcessingContext $context;

  #[\Override]
  protected function setUp(): void {
    parent::setUp();
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

  public function testProcessorsAreCalledInOrder(): void {
    $calls = [];

    $first = $this->prophesize(HtmlContentProcessor::class);
    $first->process(Argument::type(\DOMDocument::class), $this->context)
      ->will(static function () use (&$calls): void {
        $calls[] = 'first';
      })
      ->shouldBeCalledOnce();

    $second = $this->prophesize(HtmlContentProcessor::class);
    $second->process(Argument::type(\DOMDocument::class), $this->context)
      ->will(static function () use (&$calls): void {
        $calls[] = 'second';
      })
      ->shouldBeCalledOnce();

    $processor = new HtmlProcessor([$first->reveal(), $second->reveal()]);
    $processor->process('<p>test</p>', $this->context);

    self::assertSame(['first', 'second'], $calls);
  }

  public function testReturnsProcessedHtml(): void {
    $modifier = $this->prophesize(HtmlContentProcessor::class);
    $modifier->process(Argument::type(\DOMDocument::class), $this->context)
      ->will(static function (array $args): void {
        $dom = $args[0];
        $p = $dom->getElementsByTagName('p')->item(0);
        if (!($p instanceof \DOMElement)) {
            return;
        }

        $p->setAttribute('class', 'modified');
      });

    $processor = new HtmlProcessor([$modifier->reveal()]);
    $result = $processor->process('<p>hello</p>', $this->context);

    self::assertStringContainsString('class="modified"', $result);
  }

  public function testEmptyProcessorsReturnsSameHtml(): void {
    $processor = new HtmlProcessor([]);
    $result = $processor->process('<p>unchanged</p>', $this->context);

    self::assertStringContainsString('<p>unchanged</p>', $result);
  }

}
