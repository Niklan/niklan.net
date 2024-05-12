<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\BundlerResult;
use Drupal\Tests\UnitTestCase;

/**
 * Provides tests for bundle result.
 *
 * @covers \Drupal\external_content\Data\BundlerResult
 * @group external_content
 */
final class BundlerResultTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testBundleAs(): void {
    $result = BundlerResult::bundleAs('foo');

    self::assertEquals('foo', $result->bundleId);
    self::assertTrue($result->shouldBeBundled());
    self::assertFalse($result->shouldNotBeBundled());
  }

  /**
   * {@selfdoc}
   */
  public function testPass(): void {
    $result = BundlerResult::pass();

    self::assertNull($result->bundleId);
    self::assertFalse($result->shouldBeBundled());
    self::assertTrue($result->shouldNotBeBundled());
  }

}
