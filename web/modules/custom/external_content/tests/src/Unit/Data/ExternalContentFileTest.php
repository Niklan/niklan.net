<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\Data;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Provides an external content file test.
 *
 * @covers \Drupal\external_content\Data\ExternalContentFile
 * @group external_content
 */
final class ExternalContentFileTest extends UnitTestCase {

  /**
   * Tests the object.
   */
  public function testObject(): void {
    vfsStream::setup(structure: [
      'foo' => [
        'bar' => [
          'baz.txt' => 'Hello, World!',
        ],
      ],
    ]);

    $instance = new ExternalContentFile(
      vfsStream::url('root/foo'),
      vfsStream::url('root/foo/bar/baz.txt'),
    );

    self::assertEquals('vfs://root/foo/bar/baz.txt', $instance->getPathname());
    self::assertEquals('vfs://root/foo', $instance->getWorkingDir());
    self::assertEquals('bar/baz.txt', $instance->getRelativePathname());
    self::assertEquals('txt', $instance->getExtension());
    self::assertTrue($instance->isReadable());
    self::assertEquals('Hello, World!', $instance->getContents());
    self::assertInstanceOf(Data::class, $instance->getData());
  }

}
