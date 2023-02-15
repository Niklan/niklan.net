<?php declare(strict_types = 1);

namespace Drupal\Test\external_content\Unit\Dto;

use Drupal\external_content\Dto\ExternalContent;
use Drupal\external_content\Dto\ExternalContentCollection;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a test for external content collection.
 *
 * @coversDefaultClass \Drupal\external_content\Dto\ExternalContentCollection
 */
final class ExternalContentCollectionTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   */
  public function testObject(): void {
    $collection = new ExternalContentCollection();

    self::assertCount(0, $collection);
    self::assertEmpty($collection->getIterator()->getArrayCopy());
    self::assertFalse($collection->has('test'));
    self::assertNull($collection->get('test'));

    $file = new ExternalContent('test');
    $collection->add($file);

    self::assertCount(1, $collection);
    self::assertContains($file, $collection);
    self::assertTrue($collection->has('test'));
    self::assertSame($file, $collection->get('test'));
  }

}
