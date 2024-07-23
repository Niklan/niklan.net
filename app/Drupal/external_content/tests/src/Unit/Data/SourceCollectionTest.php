<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\SourceCollection;
use Drupal\external_content\Source\File;
use Drupal\Tests\UnitTestCase;

/**
 * Provides an external content file collection test.
 *
 * @covers \Drupal\external_content\Data\SourceCollection
 * @group external_content
 */
final class SourceCollectionTest extends UnitTestCase {

  public function testObject(): void {
    $collection_a = new SourceCollection();

    self::assertCount(0, $collection_a->items());
    self::assertEquals([], $collection_a->items());

    $file_a = new File('foo', 'bar', 'html');
    $collection_a->add($file_a);

    self::assertCount(1, $collection_a->items());
    self::assertEquals([$file_a], $collection_a->items());

    $collection_b = new SourceCollection();
    $file_b = new File('bar', 'baz', 'html');
    $collection_b->add($file_b);

    self::assertCount(1, $collection_b->items());
    self::assertEquals([$file_b], $collection_b->items());

    $collection_a->merge($collection_b);

    self::assertCount(2, $collection_a->items());
    self::assertEquals([$file_a, $file_b], $collection_a->items());
  }

}
