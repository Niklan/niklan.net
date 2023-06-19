<?php declare(strict_types = 1);

namespace Drupal\external_content\Finder;

use Drupal\external_content\Contract\EnvironmentInterface;
use Drupal\external_content\Contract\FinderInterface;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\ExternalContentFileCollection;
use Drupal\external_content\Exception\InvalidConfigurationException;
use Drupal\external_content\Exception\MissingConfigurationException;
use Symfony\Component\Finder\Finder;

/**
 * Provides a Markdown finder.
 */
final class MarkdownFinder implements FinderInterface {

  /**
   * The Markdown files extensions.
   */
  protected const EXTENSIONS = ['md', 'markdown'];

  /**
   * {@inheritdoc}
   */
  public function find(EnvironmentInterface $environment): ExternalContentFileCollection {
    $this->validate($environment);

    $files = new ExternalContentFileCollection();
    $configuration = $environment->getConfiguration();
    $settings = $configuration->get('markdown_finder');
    $patterns = \array_map(
      static fn ($extension) => '*.' . $extension,
      self::EXTENSIONS,
    );

    $finder = new Finder();
    $finder->in($settings['dirs']);
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

      $file = new ExternalContentFile($working_dir, $file_info->getPathname());
      $files->add($file);
    }

    return $files;
  }

  protected function validate(EnvironmentInterface $environment): void {
    $configuration = $environment->getConfiguration();

    if (!$configuration->exists('markdown_finder')) {
      $message = \sprintf(
        'To use "%s" you must provide "markdown_finder" configuration.',
        self::class,
      );

      throw new MissingConfigurationException($message);
    }

    $settings = $configuration->get('markdown_finder');

    if (!\array_key_exists('dirs', $settings)) {
      $message = \sprintf('"%s" requires "dirs" settings.', self::class);

      throw new MissingConfigurationException($message);
    }

    $dirs = $settings['dirs'];

    if (!\is_string($dirs) && !\is_array($dirs)) {
      $message = \sprintf(
        '"%s" requires "dirs" configuration to be string or array.',
        self::class,
      );

      throw new InvalidConfigurationException($message);
    }
  }

}
