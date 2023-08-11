<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\Attributes;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a test for attributes DTO.
 *
 * @covers \Drupal\external_content\Data\Attributes
 * @group external_content
 */
final class AttributesTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $instance = new Attributes();

    self::assertFalse($instance->hasAttributes());
    self::assertFalse($instance->hasAttribute('foo'));
    self::assertNull($instance->getAttribute('foo'));
    self::assertEmpty($instance->getAttributes());

    $instance->setAttribute('foo', 'bar');

    self::assertTrue($instance->hasAttributes());
    self::assertTrue($instance->hasAttribute('foo'));
    self::assertEquals('bar', $instance->getAttribute('foo'));
    self::assertEquals(['foo' => 'bar'], $instance->getAttributes());

    $instance->removeAttribute('foo');

    self::assertFalse($instance->hasAttributes());
    self::assertFalse($instance->hasAttribute('foo'));
    self::assertNull($instance->getAttribute('foo'));
    self::assertEmpty($instance->getAttributes());
  }

}
