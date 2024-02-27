<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Finder;

use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Extension\FileFinderExtension;
use Drupal\external_content\Finder\FinderManager;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use League\Config\Configuration;
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

    $configuration = new Configuration();
    $configuration->merge([
      'file_finder' => [
        'extensions' => ['md', 'html'],
        'directories' => [
          vfsStream::url('root/foo'),
          vfsStream::url('root/bar'),
          vfsStream::url('root/baz'),
        ],
      ],
    ]);
    $environment = $this->buildEnvironment($configuration);

    $finder = $this->getFinder();
    $finder->setEnvironment($environment);
    $result = $finder->find();
    // 'root/foo/baz.txt' - is ignored because of different extension.
    // 'root/foo/baz.html' - is directory and should be avoided.
    // 'root/quux/baz.html' - has a valid extension, but not listed directory.
    self::assertCount(2, $result);
  }

  /**
   * {@selfdoc}
   */
  public function testEmptyFinder(): void {
    $this->prepareDirectory();

    $configuration = new Configuration();
    $configuration->merge([
      'file_finder' => [
        'extensions' => ['md', 'html'],
        'directories' => [
          vfsStream::url('root/empty-dir'),
        ],
      ],
    ]);
    $environment = $this->buildEnvironment($configuration);

    $finder = $this->getFinder();
    $finder->setEnvironment($environment);
    $result = $finder->find();
    self::assertCount(0, $result);
  }

  /**
   * {@selfdoc}
   */
  private function buildEnvironment(Configuration $configuration): Environment {
    $environment = new Environment($configuration);
    $environment->addExtension(new FileFinderExtension());

    return $environment;
  }

  /**
   * {@selfdoc}
   */
  private function getFinder(): FinderManager {
    return $this->container->get(FinderInterface::class);
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

}
