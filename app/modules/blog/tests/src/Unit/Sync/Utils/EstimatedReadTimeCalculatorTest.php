<?php

declare(strict_types=1);

namespace Drupal\Tests\app_blog\Unit\Sync\Utils;

use Drupal\app_blog\Sync\Utils\EstimatedReadTimeCalculator;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(EstimatedReadTimeCalculator::class)]
final class EstimatedReadTimeCalculatorTest extends UnitTestCase {

  private EstimatedReadTimeCalculator $calculator;

  public function testEmptyHtml(): void {
    self::assertSame(0, $this->calculator->calculate(''));
  }

  public function testShortText(): void {
    $result = $this->calculator->calculate('<p>Hello world</p>');
    // Two words < 1 minute, rounds to 0.
    self::assertSame(0, $result);
  }

  public function testCodeBlocksCountedWithMultiplier(): void {
    $words = $this->getRandomGenerator()->sentences(50);
    $text_only = '<p>' . $words . '</p>';
    $with_code = $text_only . '<pre><code>' . $words . '</code></pre>';

    $text_result = $this->calculator->calculate($text_only);
    $code_result = $this->calculator->calculate($with_code);

    self::assertGreaterThan($text_result, $code_result);
  }

  public function testCodeBlocksExcludedFromTextCount(): void {
    $words = $this->getRandomGenerator()->sentences(50);
    $html = '<pre><code>' . $words . '</code></pre>';
    $result = $this->calculator->calculate($html);

    self::assertGreaterThan(0, $result);
  }

  public function testOneMinuteForEnoughWords(): void {
    // 143 words = exactly 1 minute at 143 WPM.
    $words = \implode(' ', \array_fill(0, 143, 'word'));
    $html = '<p>' . $words . '</p>';
    $result = $this->calculator->calculate($html);
    self::assertSame(1, $result);
  }

  #[\Override]
  protected function setUp(): void {
    parent::setUp();
    $this->calculator = new EstimatedReadTimeCalculator();
  }

}
