<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\ExternalContentBundle;
use Drupal\external_content\Data\ExternalContentBundleCollection;
use Drupal\Tests\UnitTestCase;

/**
 * Provides an external content bundle collection test.
 *
 * @covers \Drupal\external_content\Data\ExternalContentBundleCollection
 * @group external_content
 */
final class ExternalContentBundleCollectionTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $instance = new ExternalContentBundleCollection();

    self::assertCount(0, $instance);
    self::assertEquals([], $instance->getIterator()->getArrayCopy());

    $bundle = new ExternalContentBundle('foo');
    $instance->add($bundle);

    self::assertCount(1, $instance);
    self::assertEquals([$bundle], $instance->getIterator()->getArrayCopy());
  }

}
