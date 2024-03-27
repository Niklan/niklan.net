<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\IdentifiedSource;
use Drupal\Tests\UnitTestCase;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Data\IdentifiedSource
 * @group external_content
 */
final class SourceIdentificationTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $id = $this->randomString();
    $attributes = new Attributes();

    $instance = new IdentifiedSource($id, $attributes);

    self::assertEquals($id, $instance->id);
    self::assertEquals($attributes, $instance->attributes);

    // Without attribute.
    $instance = new IdentifiedSource($id);
    self::assertEquals($id, $instance->id);
    self::assertInstanceOf(Attributes::class, $instance->attributes);
  }

}
