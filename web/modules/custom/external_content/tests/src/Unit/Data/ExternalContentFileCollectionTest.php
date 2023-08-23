<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\ExternalContentFileCollection;
use Drupal\Tests\UnitTestCase;

/**
 * Provides an external content file collection test.
 *
 * @covers \Drupal\external_content\Data\ExternalContentFileCollection
 * @group external_content
 */
final class ExternalContentFileCollectionTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $collection_a = new ExternalContentFileCollection();

    self::assertCount(0, $collection_a);
    self::assertEquals([], $collection_a->getIterator()->getArrayCopy());

    $file_a = new ExternalContentFile('foo', 'bar');
    $collection_a->add($file_a);

    self::assertCount(1, $collection_a);
    self::assertEquals(
      ['bar' => $file_a],
      $collection_a->getIterator()->getArrayCopy(),
    );

    $collection_b = new ExternalContentFileCollection();
    $file_b = new ExternalContentFile('bar', 'baz');
    $collection_b->add($file_b);

    self::assertCount(1, $collection_b);
    self::assertEquals(
      ['baz' => $file_b],
      $collection_b->getIterator()->getArrayCopy(),
    );

    $collection_a->merge($collection_b);

    self::assertCount(2, $collection_a);
    self::assertEquals(
      ['bar' => $file_a, 'baz' => $file_b],
      $collection_a->getIterator()->getArrayCopy(),
    );
  }

}
