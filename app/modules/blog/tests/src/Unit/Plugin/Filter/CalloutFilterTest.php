<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Plugin\Filter;

use Drupal\app_blog\Plugin\Filter\CalloutFilter;
use Drupal\Core\Render\RendererInterface;
use Drupal\Tests\app_blog\Unit\Plugin\Filter\Stub\StubRenderer;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(CalloutFilter::class)]
final class CalloutFilterTest extends UnitTestCase {

  use ProphecyTrait;

  public function testTextWithoutCalloutPlaceholderPassedThrough(): void {
    $renderer = $this->prophesize(RendererInterface::class);
    $filter = $this->createFilter($renderer->reveal());

    $text = '<p>No callouts</p>';
    $result = $filter->process($text, 'en');

    self::assertSame($text, $result->getProcessedText());
    $renderer->renderInIsolation(Argument::any())->shouldNotHaveBeenCalled();
  }

  public function testCalloutPlaceholderReplacedWithRendered(): void {
    $renderer = $this->prophesize(RendererInterface::class);
    $matches_callout = static fn (array $build): bool => $build['#component'] === 'app_blog:callout'
      && $build['#props']['type'] === 'warning';
    $renderer->renderInIsolation(Argument::that($matches_callout))
      ->willReturn('<div class="callout-rendered">Warning content</div>');

    $filter = $this->createFilter($renderer->reveal());
    $text = '<app-callout data-type="warning"><app-callout-body><p>Warning</p></app-callout-body></app-callout>';

    $result = $filter->process($text, 'en');

    self::assertStringContainsString('callout-rendered', $result->getProcessedText());
    self::assertStringNotContainsString('<app-callout', $result->getProcessedText());
  }

  public function testCalloutSlotsExtracted(): void {
    $captured_build = NULL;
    $renderer = $this->prophesize(RendererInterface::class);
    $renderer->renderInIsolation(Argument::any())
      ->will(static function (array $args) use (&$captured_build): string {
        $captured_build = $args[0];
        return '<div>rendered</div>';
      });

    $filter = $this->createFilter($renderer->reveal());
    $text = <<<'HTML'
    <app-callout data-type="note"><app-callout-title>My Title</app-callout-title><app-callout-body><p>Body</p></app-callout-body></app-callout>
    HTML;

    $filter->process($text, 'en');

    self::assertNotNull($captured_build);
    self::assertSame('My Title', $captured_build['#slots']['title']['#markup']);
    self::assertStringContainsString('<p>Body</p>', $captured_build['#slots']['body']['#markup']);
  }

  public function testDefaultTypeIsNote(): void {
    $captured_build = NULL;
    $renderer = $this->prophesize(RendererInterface::class);
    $renderer->renderInIsolation(Argument::any())
      ->will(static function (array $args) use (&$captured_build): string {
        $captured_build = $args[0];
        return '<div>rendered</div>';
      });

    $filter = $this->createFilter($renderer->reveal());
    $text = '<app-callout><app-callout-body><p>Body</p></app-callout-body></app-callout>';

    $filter->process($text, 'en');

    self::assertNotNull($captured_build);
    self::assertSame('note', $captured_build['#props']['type']);
  }

  public function testMultipleCallouts(): void {
    $call_count = 0;
    $renderer = $this->prophesize(RendererInterface::class);
    $renderer->renderInIsolation(Argument::any())
      ->will(static function () use (&$call_count): string {
        $call_count++;
        return '<div>rendered</div>';
      });

    $filter = $this->createFilter($renderer->reveal());
    $text = <<<'HTML'
    <app-callout data-type="note"><app-callout-body>A</app-callout-body></app-callout>
    <p>separator</p>
    <app-callout data-type="tip"><app-callout-body>B</app-callout-body></app-callout>
    HTML;

    $filter->process($text, 'en');

    self::assertSame(2, $call_count);
  }

  public function testAttachmentsFromRenderedComponentPropagated(): void {
    $renderer = new StubRenderer('<div>rendered</div>', ['library' => ['app_blog/callout']]);
    $filter = $this->createFilter($renderer);

    $text = '<app-callout data-type="note"><app-callout-body>Text</app-callout-body></app-callout>';
    $result = $filter->process($text, 'en');

    self::assertSame(['library' => ['app_blog/callout']], $result->getAttachments());
  }

  public function testAttachmentsMergedFromMultipleCallouts(): void {
    $renderer = new StubRenderer('<div>rendered</div>');
    $filter = $this->createFilter($renderer);

    $text = <<<'HTML'
    <app-callout data-type="note"><app-callout-body>A</app-callout-body></app-callout>
    <app-callout data-type="tip"><app-callout-body>B</app-callout-body></app-callout>
    HTML;

    $result = $filter->process($text, 'en');

    self::assertContains('test/lib-1', $result->getAttachments()['library']);
    self::assertContains('test/lib-2', $result->getAttachments()['library']);
  }

  private function createFilter(RendererInterface $renderer): CalloutFilter {
    return new CalloutFilter(
      [], 'app_blog_callout', ['provider' => 'app_blog'],
      $renderer,
    );
  }

}
