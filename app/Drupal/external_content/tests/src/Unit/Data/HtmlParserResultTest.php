<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Node\PlainText;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a HTML parser result test.
 *
 * @covers \Drupal\external_content\Data\HtmlParserResult
 * @group external_content
 */
final class HtmlParserResultTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testReplace(): void {
    $replacement = new PlainText('foo');
    $result = HtmlParserResult::replace($replacement);

    self::assertFalse($result->shouldContinue());
    self::assertTrue($result->shouldNotContinue());
    self::assertTrue($result->hasReplacement());
    self::assertFalse($result->hasNoReplacement());
    self::assertEquals($replacement, $result->replacement());
  }

  /**
   * {@selfdoc}
   */
  public function testStop(): void {
    $result = HtmlParserResult::stop();

    self::assertFalse($result->shouldContinue());
    self::assertTrue($result->shouldNotContinue());
    self::assertFalse($result->hasReplacement());
    self::assertTrue($result->hasNoReplacement());
    self::assertNull($result->replacement());
  }

  /**
   * {@selfdoc}
   */
  public function testPass(): void {
    $result = HtmlParserResult::pass();

    self::assertTrue($result->shouldContinue());
    self::assertFalse($result->shouldNotContinue());
    self::assertFalse($result->hasReplacement());
    self::assertTrue($result->hasNoReplacement());
    self::assertNull($result->replacement());
  }

}
