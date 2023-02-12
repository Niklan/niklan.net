<?php

declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Dto;

use Drupal\external_content\Dto\SourceFile;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Validates that source file DTO works as expected.
 *
 * @coversDefaultClass \Drupal\external_content\Dto\SourceFile
 */
final class SourceFileTest extends UnitTestCase {

  /**
   * Tests class functionality.
   */
  public function testClass(): void {
    vfsStream::setup(structure: [
      'file.txt' => 'content',
      'foo' => [
        'bar' => [
          'baz.en.txt' => 'baz content',
        ],
      ],
    ]);

    $source_file = new SourceFile(
      vfsStream::url('root'),
      vfsStream::url('root/file.txt'),
    );

    $this->assertEquals('vfs://root', $source_file->getWorkingDir());
    $this->assertEquals('vfs://root/file.txt', $source_file->getPathname());
    $this->assertEquals('file.txt', $source_file->getRelativePathname());
    $this->assertEquals('txt', $source_file->getExtension());
    $this->assertTrue($source_file->isReadable());
    $this->assertEquals('content', $source_file->getContents());

    $source_file = new SourceFile(
      vfsStream::url('root'),
      vfsStream::url('root/foo/bar/baz.en.txt'),
    );

    $this->assertEquals('vfs://root', $source_file->getWorkingDir());
    $this->assertEquals(
      'vfs://root/foo/bar/baz.en.txt',
      $source_file->getPathname(),
    );
    $this->assertEquals(
      'foo/bar/baz.en.txt',
      $source_file->getRelativePathname(),
    );
    $this->assertEquals('txt', $source_file->getExtension());
    $this->assertTrue($source_file->isReadable());
    $this->assertEquals('baz content', $source_file->getContents());
  }

}
