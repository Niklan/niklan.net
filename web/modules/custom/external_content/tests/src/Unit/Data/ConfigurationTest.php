<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\Tests\UnitTestCase;

/**
 * Provides test for configuration object.
 *
 * @covers \Drupal\external_content\Data\Configuration
 * @group external_content
 */
final class ConfigurationTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $configuration = new Configuration(['foo' => 'bar']);

    self::assertTrue($configuration->exists('foo'));
    self::assertEquals('bar', $configuration->get('foo'));

    self::assertFalse($configuration->exists('bar'));
    self::assertNull($configuration->get('bar'));
  }

}
