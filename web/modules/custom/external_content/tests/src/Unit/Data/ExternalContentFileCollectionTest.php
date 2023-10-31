<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Source\Collection;
use Drupal\external_content\Source\File;
use Drupal\Tests\UnitTestCase;

/**
 * Provides an external content file collection test.
 *
 * @covers \Drupal\external_content\Source\Collection
 * @group external_content
 */
final class ExternalContentFileCollectionTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    $collection_a = new Collection();

    self::assertCount(0, $collection_a);
    self::assertEquals([], $collection_a->getIterator()->getArrayCopy());

    $file_a = new File('foo', 'bar', 'html');
    $collection_a->add($file_a);

    self::assertCount(1, $collection_a);
    self::assertEquals(
      ['bar' => $file_a],
      $collection_a->getIterator()->getArrayCopy(),
    );

    $collection_b = new Collection();
    $file_b = new File('bar', 'baz', 'html');
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
