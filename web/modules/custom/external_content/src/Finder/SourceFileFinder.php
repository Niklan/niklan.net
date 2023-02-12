<?php

declare(strict_types = 1);

namespace Drupal\external_content\Finder;

use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileCollection;
use Symfony\Component\Finder\Finder;

/**
 * Represents a default source file finder.
 */
final class SourceFileFinder implements SourceFileFinderInterface {

  /**
   * The list of supported extensions.
   *
   * @todo Prepare this list from a Markup plugins.
   */
  protected const SUPPORTED_EXTENSIONS = ['.md', '.markdown'];

  /**
   * {@inheritdoc}
   */
  public function find(string $working_dir): SourceFileCollection {
    $patterns = \array_map(
      static fn ($extension) => '*' . $extension,
      self::SUPPORTED_EXTENSIONS,
    );

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
