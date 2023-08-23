<?php declare(strict_types = 1);

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
   *
   * @covers \Drupal\external_content\Data\HtmlParserResultContinue
   */
  public function testContinue(): void {
    $result = HtmlParserResult::continue();

    self::assertTrue($result->shouldContinue());
    self::assertFalse($result->shouldNotContinue());
    self::assertFalse($result->hasReplacement());
    self::assertNull($result->getReplacement());
  }

  /**
   * {@selfdoc}
   *
   * @covers \Drupal\external_content\Data\HtmlParserResultFinalize
   */
  public function testFinalize(): void {
    $replacement = new PlainText('foo');
    $result = HtmlParserResult::finalize($replacement);

    self::assertFalse($result->shouldContinue());
    self::assertTrue($result->shouldNotContinue());
    self::assertTrue($result->hasReplacement());
    self::assertEquals($replacement, $result->getReplacement());
  }

  /**
   * {@selfdoc}
   *
   * @covers \Drupal\external_content\Data\HtmlParserResultReplace
   */
  public function testReplace(): void {
    $replacement = new PlainText('foo');
    $result = HtmlParserResult::replace($replacement);

    self::assertTrue($result->shouldContinue());
    self::assertFalse($result->shouldNotContinue());
    self::assertTrue($result->hasReplacement());
    self::assertEquals($replacement, $result->getReplacement());
  }

  /**
   * {@selfdoc}
   *
   * @covers \Drupal\external_content\Data\HtmlParserResultStop
   */
  public function testStop(): void {
    $result = HtmlParserResult::stop();

    self::assertFalse($result->shouldContinue());
    self::assertTrue($result->shouldNotContinue());
    self::assertFalse($result->hasReplacement());
    self::assertNull($result->getReplacement());
  }

}
