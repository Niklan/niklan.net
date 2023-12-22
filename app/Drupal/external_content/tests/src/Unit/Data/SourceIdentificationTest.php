<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\SourceIdentification;
use Drupal\external_content_test\Source\FooSource;
use Drupal\Tests\UnitTestCase;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Data\SourceIdentification
 * @group external_content
 */
final class SourceIdentificationTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $id = $this->randomString();
    $attributes = new Attributes();

    $instance = new SourceIdentification($id, $attributes);

    self::assertEquals($id, $instance->id);
    self::assertEquals($attributes, $instance->attributes);

    // Without attribute.
    $instance = new SourceIdentification($id);
    self::assertEquals($id, $instance->id);
    self::assertInstanceOf(Attributes::class, $instance->attributes);
  }

}
