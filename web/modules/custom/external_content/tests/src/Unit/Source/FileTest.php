<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Source;

use Drupal\external_content\Data\Data;
use Drupal\external_content\Source\File;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Provides an external content file test.
 *
 * @covers \Drupal\external_content\Source\File
 * @group external_content
 */
final class FileTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testObject(): void {
    vfsStream::setup(structure: [
      'foo' => [
        'bar' => [
          'baz.txt' => 'Hello, World!',
        ],
      ],
    ]);

    $instance = new File(
      vfsStream::url('root/foo'),
      vfsStream::url('root/foo/bar/baz.txt'),
      'text',
    );

    self::assertEquals('vfs://root/foo/bar/baz.txt', $instance->getPathname());
    self::assertEquals('vfs://root/foo', $instance->getWorkingDir());
    self::assertEquals('bar/baz.txt', $instance->getRelativePathname());
    self::assertEquals('txt', $instance->getExtension());
    self::assertTrue($instance->isReadable());
    self::assertEquals('Hello, World!', $instance->contents());
    self::assertInstanceOf(Data::class, $instance->data());
  }

}
