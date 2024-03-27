<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\ContentBundle;
use Drupal\external_content\Data\SourceBundleCollection;
use Drupal\Tests\UnitTestCase;

/**
 * Provides an external content bundle collection test.
 *
 * @covers \Drupal\external_content\Data\SourceBundleCollection
 * @group external_content
 */
final class ExternalContentBundleCollectionTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $instance = new SourceBundleCollection();

    self::assertCount(0, $instance);
    self::assertEquals([], $instance->getIterator()->getArrayCopy());

    $bundle = new ContentBundle('foo');
    $instance->add($bundle);

    self::assertCount(1, $instance);
    self::assertEquals([$bundle], $instance->getIterator()->getArrayCopy());
  }

}
