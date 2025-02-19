<?php

declare(strict_types=1);

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

  public function testObject(): void {
    $instance = new Attributes();

    self::assertFalse($instance->hasAttributes());
    self::assertFalse($instance->hasAttribute('foo'));
    $this->expectExceptionObject(new \OutOfBoundsException("The offset \"foo\" does not exist."));
    self::assertNull($instance->getAttribute('foo'));
    self::assertEmpty($instance->all());

    $instance->setAttribute('foo', 'bar');

    self::assertTrue($instance->hasAttributes());
    self::assertTrue($instance->hasAttribute('foo'));
    self::assertEquals('bar', $instance->getAttribute('foo'));
    self::assertEquals(['foo' => 'bar'], $instance->all());

    $instance->removeAttribute('foo');

    self::assertFalse($instance->hasAttributes());
    self::assertFalse($instance->hasAttribute('foo'));
    self::assertNull($instance->getAttribute('foo'));
    self::assertEmpty($instance->all());
  }

}
