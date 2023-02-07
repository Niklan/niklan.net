<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Finder;

use Drupal\external_content\Finder\SourceFileFinder;
use Drupal\Tests\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Validates that source content finder works as expected.
 *
 * @coversDefaultClass \Drupal\external_content\Finder\SourceFileFinder
 */
final class SourceFileFinderTest extends UnitTestCase {

  /**
   * Tests a class instance.
   */
  public function testFinder(): void {
    vfsStream::setup(structure: [
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
    ]);

    $finder = new SourceFileFinder();
    $source_files = $finder->find(vfsStream::url('root'));
    $this->assertEquals(6, $source_files->count());

    /** @var \Drupal\external_content\Dto\SourceFile $sixth_file */
    $sixth_file = $source_files->getIterator()->offsetGet(5);
    $this->assertEquals('file with a suffix', $sixth_file->getContents());
    $this->assertEquals('baz.md/bar.en.md', $sixth_file->getRelativePathname());

    $source_files = $finder->find(vfsStream::url('root/foo/bar/baz'));
    $this->assertEquals(2, $source_files->count());
  }

}
