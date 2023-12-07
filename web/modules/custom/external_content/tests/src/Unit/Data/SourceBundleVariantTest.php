<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\IdentifierSource;
use Drupal\external_content_test\Source\FooSource;
use Drupal\Tests\UnitTestCase;

/**
 * Provides an external content bundle document test.
 *
 * @covers \Drupal\external_content\Data\IdentifierSource
 * @group external_content
 */
final class SourceBundleVariantTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $source = new FooSource('id', 'type', 'contents');
    $attributes = new Attributes();

    $instance = new IdentifierSource($source, $attributes);

    self::assertEquals($source, $instance->source);
    self::assertEquals($attributes, $instance->attributes);

    // Without attribute.
    $instance = new IdentifierSource($source);
    self::assertEquals($source, $instance->source);
    self::assertInstanceOf(Attributes::class, $instance->attributes);
  }

}
