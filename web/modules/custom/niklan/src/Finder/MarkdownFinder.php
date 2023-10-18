<?php declare(strict_types = 1);

namespace Drupal\niklan\Finder;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\ExternalContentFileCollection;
use Drupal\external_content\Exception\InvalidConfigurationException;
use Drupal\external_content\Exception\MissingConfigurationException;
use Symfony\Component\Finder\Finder;

/**
 * Provides a Markdown finder.
 */
final class MarkdownFinder implements FinderInterface, EnvironmentAwareInterface {

  /**
   * The Markdown files extensions.
   */
  protected const EXTENSIONS = ['md', 'markdown'];

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function find(): ExternalContentFileCollection {
    $this->assertConfiguration();

    $files = new ExternalContentFileCollection();
    $configuration = $this->environment->getConfiguration();
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

  /**
   * Validates configuration for the finder.
   */
  protected function assertConfiguration(): void {
    $configuration = $this->environment->getConfiguration();

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

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
