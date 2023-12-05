<?php declare(strict_types = 1);

namespace Drupal\external_content\Finder;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Data\SourceCollection;
use Drupal\external_content\Event\FileFoundEvent;
use Drupal\external_content\Source\File;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Mime\MimeTypeGuesserInterface;

/**
 * Provides a file finder.
 *
 * Configuration:
 * - file_finder:
 *   - extensions: An array with extensions to search for. E.g.: ['md', 'html'].
 *   - directories: An array with directories to search into.
 */
final class FileFinder implements FinderInterface, EnvironmentAwareInterface, ContainerAwareInterface {

  /**
   * {@selfdoc}
   */
  protected EnvironmentInterface $environment;

  /**
   * {@selfdoc}
   */
  protected ContainerInterface $container;

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

    $mime_type_guesser = $this->container->get('file.mime_type.guesser');
    \assert($mime_type_guesser instanceof MimeTypeGuesserInterface);

    foreach ($finder as $file_info) {
      // Directories named as files should be avoided. E.g.
      // "directory-name.md/file-name.md".
      if (!$file_info->isFile()) {
        continue;
      }

      $working_dir = \str_replace(
        search: $file_info->getRelativePath(),
        replace: '',
        subject: $file_info->getPath(),
      );
      $working_dir = \rtrim($working_dir, '/');

      $file = new File(
        workingDir: $working_dir,
        pathname: $file_info->getPathname(),
        // Guessed MIME by Drupal core can be a complete random. It is highly
        // recommended to use drupal/sophron with sophron_guesser submodule
        // enabled. But this is optionally, use only when this guesser fails.
        // E.g.: it doesn't handle Markdown files at all, Drupal doesn't even
        // know that this type exists.
        type: $mime_type_guesser->guessMimeType($file_info->getPathname()),
      );

      $event = new FileFoundEvent($file);
      $this->environment->dispatch($event);

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

  /**
   * {@inheritdoc}
   */
  public function setContainer(?ContainerInterface $container): void {
    $this->container = $container;
  }

}
