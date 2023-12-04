<?php declare(strict_types = 1);

namespace Drupal\external_content\Finder;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Data\SourceCollection;
use Drupal\external_content\Source\File;
use Symfony\Component\Finder\Finder;

/**
 * Provides a file finder.
 *
 * Configuration:
 * - file_finder:
 *   - extensions: An array with extensions to search for. E.g.: ['md', 'html'].
 *   - directories: An array with directories to search into.
 */
final class FileFinder implements FinderInterface, EnvironmentAwareInterface {

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function find(): SourceCollection {
    $files = new SourceCollection();
    $configuration = $this->environment->getConfiguration();
    $patterns = \array_map(
      static fn ($extension) => '*.' . $extension,
      $configuration->get('file_finder.extensions'),
    );

    $finder = new Finder();
    $finder->in($configuration->get('file_finder.directories'));
    $finder->name($patterns);

    if (!$finder->hasResults()) {
      return $files;
    }

    foreach ($finder as $file_info) {
      // Directories named as files should be avoided. E.g.
      // "directory-name.md/file-name.md".
      if (!$file_info->isFile()) {
        continue;
      }

      $working_dir = \str_replace(
        $file_info->getRelativePath(),
        '',
        $file_info->getPath(),
      );
      $working_dir = \rtrim($working_dir, '/');

      $file = new File(
        $working_dir,
        $file_info->getPathname(),
        'html',
      );
      $files->add($file);
    }

    return $files;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
