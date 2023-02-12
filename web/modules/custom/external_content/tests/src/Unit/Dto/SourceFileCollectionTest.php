<?php

declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Dto;

use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileCollection;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Validates that collection properly handles source files.
 *
 * @coversDefaultClass \Drupal\external_content\Dto\SourceFileCollection
 */
final class SourceFileCollectionTest extends UnitTestCase {

  /**
   * Tests class functionality.
   */
  public function testClass(): void {
    vfsStream::setup(structure: [
      'file-a.txt' => 'content-a',
      'file-b.txt' => 'content-b',
    ]);

    $source_file_a = new SourceFile(
      vfsStream::url('root'),
      vfsStream::url('root/file-a.txt'),
    );
    $source_file_b = new SourceFile(
      vfsStream::url('root'),
      vfsStream::url('root/file-b.txt'),
    );

    $collection = new SourceFileCollection();
    $this->assertEquals(0, $collection->count());

    $collection->add($source_file_a);
    $collection->add($source_file_b);
    $this->assertEquals(2, $collection->count());

    $this->assertEquals(
      $source_file_a,
      $collection->getIterator()->offsetGet(0),
    );
    $this->assertEquals(
      $source_file_b,
      $collection->getIterator()->offsetGet(1),
    );
  }

}
