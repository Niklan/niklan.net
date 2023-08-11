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
   * Tests identified result.
   *
   * @covers \Drupal\external_content\Data\BundlerResultIdentified
   */
  public function testIdentified(): void {
    $attributes = new Attributes();
    $result = BundlerResult::identified('foo', $attributes);

    self::assertEquals('foo', $result->id());
    self::assertEquals($attributes, $result->attributes());
    self::assertTrue($result->isIdentified());
    self::assertFalse($result->isUnidentified());
  }

  /**
   * Tests unidentified result.
   *
   * @covers \Drupal\external_content\Data\BundlerResultUnidentified
   */
  public function testUnidentified(): void {
    $result = BundlerResult::unidentified();

    self::assertTrue($result->isUnidentified());
    self::assertFalse($result->isIdentified());
  }

}
