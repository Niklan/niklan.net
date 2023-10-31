<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Extension;

use Drupal\external_content\Environment\Environment;
use Drupal\external_content_test\Extension\ConfigurableExtension;
use Drupal\Tests\UnitTestCase;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content_test\Extension\ConfigurableExtension
 * @group external_content
 */
final class ConfigurableExtensionTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testExtension(): void {
    $environment = new Environment();
    $environment->addExtension(new ConfigurableExtension());

    $environment->getConfiguration()->set('foo', 'bar');
    self::assertSame('bar', $environment->getConfiguration()->get('foo'));

    $environment->getConfiguration()->set('foo', 123);
    self::expectExceptionMessage(
      "The item 'foo' expects to be string, 123 given.",
    );
    $environment->getConfiguration()->get('foo');
  }

}
