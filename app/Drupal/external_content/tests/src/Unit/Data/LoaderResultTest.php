<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\LoaderResult;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a loader result DTO test.
 *
 * @covers \Drupal\external_content\Data\LoaderResult
 * @group external_content
 */
final class LoaderResultTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testWithResults(): void {
    $results = [
      'node' => '1',
      'media' => ['1', '2', '3'],
    ];
    $result = LoaderResult::withResults('bundle_id', $results);

    self::assertSame('bundle_id', $result->bundleId());
    self::assertFalse($result->shouldContinue());
    self::assertTrue($result->shouldNotContinue());
    self::assertSame($results, $result->results());
    self::assertTrue($result->hasResults());
    self::assertFalse($result->hasNoResults());
  }

  /**
   * {@selfdoc}
   */
  public function testPass(): void {
    $result = LoaderResult::pass('bundle_id');

    self::assertSame('bundle_id', $result->bundleId());
    self::assertTrue($result->shouldContinue());
    self::assertFalse($result->shouldNotContinue());
    self::assertEmpty($result->results());
    self::assertFalse($result->hasResults());
    self::assertTrue($result->hasNoResults());
  }

  /**
   * {@selfdoc}
   */
  public function testStop(): void {
    $result = LoaderResult::stop('bundle_id');

    self::assertSame('bundle_id', $result->bundleId());
    self::assertFalse($result->shouldContinue());
    self::assertTrue($result->shouldNotContinue());
    self::assertEmpty($result->results());
    self::assertFalse($result->hasResults());
    self::assertTrue($result->hasNoResults());
  }

}
