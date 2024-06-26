<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\IdentifiedSource;
use Drupal\Tests\UnitTestCase;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Data\IdentifiedSource
 * @group external_content
 */
final class IdentifiedSourceTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $source = $this->prophesize(SourceInterface::class)->reveal();
    $id = $this->randomString();
    $attributes = new Attributes();
    $instance = new IdentifiedSource($id, $source, $attributes);

    self::assertSame($source, $instance->source);
    self::assertEquals($id, $instance->id);
    self::assertEquals($attributes, $instance->attributes);

    // Without attributes.
    $instance = new IdentifiedSource($id, $source);
    self::assertSame($source, $instance->source);
    self::assertEquals($id, $instance->id);
    self::assertInstanceOf(Attributes::class, $instance->attributes);
  }

}
