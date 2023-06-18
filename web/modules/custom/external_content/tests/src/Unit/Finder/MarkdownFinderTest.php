<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Finder;

use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Finder\MarkdownFinder;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * @covers \Drupal\external_content\Finder\MarkdownFinder
 */
final class MarkdownFinderTest extends UnitTestCase {

  public function testFind(): void {
    vfsStream::setup(structure: [
      'directory_a' => [
        'foo' => [
          'bar' => [
            'baz' => [
              'foo.md' => 'foo content',
              'bar.markdown' => 'bar content',
              'baz.txt' => 'baz content',
            ],
            'foo.md' => 'second foo content',
          ],
        ],
        'bar.md' => 'second bar content',
        // This is a directory named as source content file.
        'baz.md' => [
          'foo.md' => 'third foo content',
          'bar.en.md' => 'file with a suffix',
        ],
      ],
      'directory_b' => [
        'bar.md' => 'another bar content in a different dir',
      ],
    ]);

    $configuration = new Configuration([
      'markdown_finder' => [
        'dirs' => [
          vfsStream::url('root/directory_a'),
          vfsStream::url('root/directory_b'),
        ],
      ],
    ]);
    $environment = new Environment($configuration);
    $markdown_finder = new MarkdownFinder();
    $files = $markdown_finder->find($environment);

    self::assertCount(7, $files);
  }

}
