<?php

declare(strict_types=1);

namespace Drupal\external_content\Finder;

use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileCollection;
use Symfony\Component\Finder\Finder;

/**
 * Provides a finder for content source files.
 */
final class SourceFileFinder {

  /**
   * The list of supported extensions.
   */
  protected const SUPPORTED_EXTENSIONS = ['.md', '.markdown'];

  /**
   * Searches for source files.
   *
   * @param string $working_dir
   *   The working dir.
   *
   * @return \Drupal\external_content\Dto\SourceFileCollection
   *   The found file collection.
   */
  public function find(string $working_dir): SourceFileCollection {
    $patterns = \array_map(static fn($extension) => '*' . $extension, self::SUPPORTED_EXTENSIONS);

    $finder = new Finder();
    $finder->in($working_dir);
    $finder->name($patterns);

    $source_files = new SourceFileCollection();

    if ($finder->hasResults()) {
      foreach ($finder as $file_info) {
        // Directories named as files should be avoided. E.g.
        // "directory-name.md/file-name.md".
        if (!$file_info->isFile()) {
          continue;
        }

        $source_file = new SourceFile($working_dir, $file_info->getPathname());
        $source_files->add($source_file);
      }
    }

    return $source_files;
  }

}
