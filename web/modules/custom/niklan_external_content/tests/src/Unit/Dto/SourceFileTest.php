<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan_external_content\Unit\Dto;

use Drupal\niklan_external_content\Dto\SourceFile;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Validates that source file DTO works as expected.
 *
 * @coversDefaultClass \Drupal\niklan_external_content\Dto\SourceFile
 */
final class SourceFileTest extends UnitTestCase {

  /**
   * Tests class functionality.
   */
  public function testClass(): void {
    vfsStream::setup();
    vfsStream::create([
      'file.txt' => 'content',
    ]);

    $source_file = new SourceFile(vfsStream::url('root/file.txt'));

    $this->assertTrue($source_file->isReadable());
    $this->assertEquals('content', $source_file->getContents());
    $this->assertEquals(vfsStream::url('root/file.txt'), $source_file->getRealpath());

    $serialized = \serialize($source_file);
    $unserialized = \unserialize($serialized, [
      'allowed_classes' => [SourceFile::class],
    ]);

    // Serialization and unserialization must properly handle SplFileObject.
    $this->assertTrue($unserialized->isReadable());
  }

}
