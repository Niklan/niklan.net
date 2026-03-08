<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Sync\Utils;

use Drupal\app_blog\Sync\Utils\TableOfContentsBuilder;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TableOfContentsBuilder::class)]
final class TableOfContentsBuilderTest extends UnitTestCase {

  private TableOfContentsBuilder $builder;

  public function testEmptyHtmlReturnsEmpty(): void {
    self::assertSame([], $this->builder->build(''));
  }

  public function testHtmlWithoutPermalinksReturnsEmpty(): void {
    $html = '<h2>Title</h2><p>Text</p>';
    self::assertSame([], $this->builder->build($html));
  }

  public function testSingleH2Heading(): void {
    $html = '<h2><a class="heading-permalink" href="#intro"></a>Introduction</h2>';

    $result = $this->builder->build($html);

    self::assertCount(1, $result);
    self::assertSame('Introduction', $result[0]['text']);
    self::assertSame('#intro', $result[0]['anchor']);
    self::assertSame(0, $result[0]['indent']);
  }

  public function testMultipleHeadingLevels(): void {
    $html = <<<'HTML'
    <h2><a class="heading-permalink" href="#one"></a>One</h2>
    <h3><a class="heading-permalink" href="#two"></a>Two</h3>
    <h4><a class="heading-permalink" href="#three"></a>Three</h4>
    HTML;

    $result = $this->builder->build($html);

    self::assertCount(3, $result);
    self::assertSame(0, $result[0]['indent']);
    self::assertSame(1, $result[1]['indent']);
    self::assertSame(2, $result[2]['indent']);
  }

  public function testH5AndH6Indents(): void {
    $html = <<<'HTML'
    <h5><a class="heading-permalink" href="#h5"></a>H5</h5>
    <h6><a class="heading-permalink" href="#h6"></a>H6</h6>
    HTML;

    $result = $this->builder->build($html);

    self::assertSame(3, $result[0]['indent']);
    self::assertSame(4, $result[1]['indent']);
  }

  public function testHeadingWithInlineElements(): void {
    $html = '<h2><a class="heading-permalink" href="#code"></a>Using <code>array_map</code> function</h2>';

    $result = $this->builder->build($html);

    self::assertCount(1, $result);
    self::assertSame('Using  function', $result[0]['text']);
  }

  public function testPermalinkInsideNonHeadingIgnored(): void {
    $html = '<p><a class="heading-permalink" href="#fake"></a>Not a heading</p>';

    $result = $this->builder->build($html);

    self::assertSame([], $result);
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();
    $this->builder = new TableOfContentsBuilder();
  }

}
