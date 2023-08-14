<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\Data;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a test for data value object.
 *
 * @covers \Drupal\external_content\Data\Data
 * @group external_content
 */
final class DataTest extends UnitTestCase {

  /**
   * Tests the object.
   */
  public function testObject(): void {
    $initial_data = ['foo' => 'bar'];
    $instance = new Data($initial_data);

    self::assertTrue($instance->has('foo'));
    self::assertEquals($initial_data['foo'], $instance->get('foo'));

    self::assertFalse($instance->has('bar'));
    self::assertNull($instance->get('bar'));

    $instance->set('bar', 'baz');

    self::assertTrue($instance->has('bar'));
    self::assertEquals('baz', $instance->get('bar'));
  }

}
