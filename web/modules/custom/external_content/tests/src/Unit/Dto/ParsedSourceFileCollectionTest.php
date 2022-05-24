<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Dto;

use Drupal\external_content\Dto\ParsedSourceFile;
use Drupal\external_content\Dto\ParsedSourceFileCollection;
use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileContent;
use Drupal\external_content\Dto\SourceFileParams;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Validates that collection properly handles parsed source files.
 *
 * @coversDefaultClass \Drupal\external_content\Dto\ParsedSourceFileCollection
 */
final class ParsedSourceFileCollectionTest extends UnitTestCase {

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

    $parsed_source_file_a = new ParsedSourceFile(
      $source_file_a,
      new SourceFileParams(['id' => 'a']),
      new SourceFileContent('foo'),
    );

    $parsed_source_file_b = new ParsedSourceFile(
      $source_file_b,
      new SourceFileParams(['id' => 'b']),
      new SourceFileContent('bar'),
    );

    $collection = new ParsedSourceFileCollection();
    $this->assertEquals(0, $collection->count());

    $collection->add($parsed_source_file_a);
    $collection->add($parsed_source_file_b);
    $this->assertEquals(2, $collection->count());

    $this->assertEquals($parsed_source_file_a, $collection->getIterator()->offsetGet(0));
    $this->assertEquals($parsed_source_file_b, $collection->getIterator()->offsetGet(1));
  }

}
