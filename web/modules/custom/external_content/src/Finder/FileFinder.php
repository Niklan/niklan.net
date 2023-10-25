<?php declare(strict_types = 1);

namespace Drupal\external_content\Finder;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Data\Data;
use Drupal\external_content\Exception\InvalidConfigurationException;
use Drupal\external_content\Exception\MissingConfigurationException;
use Drupal\external_content\Source\Collection;
use Drupal\external_content\Source\File;
use Symfony\Component\Finder\Finder;

/**
 * Provides a file finder.
 *
 * Configuration:
 * - file_finders:
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
  public function find(): Collection {
    $this->assertConfiguration();

    $files = new Collection();
    $configuration = $this->environment->getConfiguration();
    $settings = $configuration->get('file_finder');
    $patterns = \array_map(
      static fn ($extension) => '*.' . $extension,
      $settings['extensions'],
    );

    $finder = new Finder();
    $finder->in($settings['directories']);
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
        new Data(['is_markdown' => TRUE]),
      );
      $files->add($file);
    }

    return $files;
  }

  /**
   * Validates configuration for the finder.
   */
  protected function assertConfiguration(): void {
    $configuration = $this->environment->getConfiguration();

    if (!$configuration->exists('file_finder')) {
      $message = \sprintf(
        'To use "%s" you must provide "file_finder" configuration.',
        self::class,
      );

      throw new MissingConfigurationException($message);
    }

    $settings = $configuration->get('file_finder');

    if (!\array_key_exists('directories', $settings)) {
      $message = \sprintf('"%s" requires "directories" settings.', self::class);

      throw new MissingConfigurationException($message);
    }

    $directories = $settings['directories'];

    if (!\is_string($directories) && !\is_array($directories)) {
      $message = \sprintf(
        '"%s" requires "directories" configuration to be string or array.',
        self::class,
      );

      throw new InvalidConfigurationException($message);
    }

    $extensions = $settings['extensions'];

    if (!\is_array($extensions)) {
      $message = \sprintf(
        '"%" requires "extensions" configuration be an array of strings.',
        self::class,
      );

      throw new InvalidConfigurationException($message);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
