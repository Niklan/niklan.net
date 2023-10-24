<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Kernel\Finder;

use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Finder\MarkdownFinder;
use Drupal\Tests\niklan\Kernel\NiklanTestBase;
use org\bovigo\vfs\vfsStream;

/**
 * Provides a test for Markdown finder.
 *
 * @covers \Drupal\niklan\Finder\FileFinder
 * @group external_content
 */
final class MarkdownFinderTest extends NiklanTestBase {

  /**
   * {@selfdoc}
   */
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

    $markdown_finder = new MarkdownFinder();
    $markdown_finder->setEnvironment(new Environment($configuration));
    $files = $markdown_finder->find();

    self::assertCount(7, $files);
  }

}
