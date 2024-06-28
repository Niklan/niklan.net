<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Kernel\Finder;

use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\FileFinderExtension;
use Drupal\external_content\Finder\FileFinder;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use org\bovigo\vfs\vfsStream;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Finder\FileFinder
 * @group external_content
 */
final class FileFinderTest extends ExternalContentTestBase {

  /**
   * {@selfdoc}
   */
  public function testFinder(): void {
    $this->prepareDirectory();

    $configuration = [
      'file_finder' => [
        'extensions' => ['md', 'html'],
        'directories' => [
          vfsStream::url('root/foo'),
          vfsStream::url('root/bar'),
          vfsStream::url('root/baz'),
        ],
      ],
    ];
    $environment = $this->buildEnvironment($configuration);

    $finder = $this->getFinder();
    $finder->setEnvironment($environment);
    $result = $finder->find();
    // 'root/foo/baz.txt' - is ignored because of different extension.
    // 'root/foo/baz.html' - is directory and should be avoided.
    // 'root/quux/baz.html' - has a valid extension, but not listed directory.
    self::assertCount(2, $result->results()->items());
  }

  /**
   * {@selfdoc}
   */
  private function prepareDirectory(): void {
    vfsStream::setup(structure: [
      'foo' => [
        'baz.txt' => 'Hello, World!',
        'baz.html' => [],
      ],
      'bar' => [
        'baz.html' => 'Hello, World!',
      ],
      'baz' => [
        'baz.md' => 'Hello, World!',
      ],
      'quux' => [
        'baz.html' => 'Hello, World!',
      ],
      'empty-dir' => [],
    ]);
  }

  /**
   * {@selfdoc}
   */
  private function buildEnvironment(array $configuration): Environment {
    $extension = new FileFinderExtension($this->getFinder());

    $environment = new Environment('test', $configuration);
    $environment->addExtension($extension);

    return $environment;
  }

  /**
   * {@selfdoc}
   */
  private function getFinder(): FileFinder {
    return $this->container->get(FileFinder::class);
  }

  /**
   * {@selfdoc}
   */
  public function testEmptyFinder(): void {
    $this->prepareDirectory();

    $configuration = [
      'file_finder' => [
        'extensions' => ['md', 'html'],
        'directories' => [
          vfsStream::url('root/empty-dir'),
        ],
      ],
    ];
    $environment = $this->buildEnvironment($configuration);

    $finder = $this->getFinder();
    $finder->setEnvironment($environment);
    $result = $finder->find();
    self::assertTrue($result->hasNoResults());
  }

}
