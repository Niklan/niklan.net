<?php declare(strict_types = 1);

namespace Drupal\content_export\Exporter;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\content_export\Data\ExportState;
use Drupal\Core\File\FileSystemInterface;

/**
 * Provides a content exporter.
 */
final class ContentExporter {

  /**
   * Constructs a new ContentExporter instance.
   *
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   The file system.
   * @param \Drupal\content_export\Exporter\BlogEntryExporter $blogEntryExporter
   *   The blog entry exporter.
   */
  public function __construct(
    protected FileSystemInterface $fileSystem,
    protected BlogEntryExporter $blogEntryExporter,
  ) {}

  /**
   * Exports content.
   *
   * @param \Drupal\content_export\Data\ExportState $state
   *   The export state.
   */
  public function export(ExportState $state): void {
    $message = (string) new FormattableMarkup('Export has been started into @dir directory.', [
      '@dir' => $state->getDestination(),
    ]);
    $state->getOutput()->write($message, TRUE);

    $this->prepareDestinationDirectory($state);
    $this->exportBlogEntries($state);
  }

  /**
   * Prepares destination directory.
   *
   * @param \Drupal\content_export\Data\ExportState $state
   *   The export state.
   */
  protected function prepareDestinationDirectory(ExportState $state): void {
    if (\is_dir($state->getDestination())) {
      $message = (string) new FormattableMarkup('The destination directory (@dir) is already exists, removing.', [
        '@dir' => $state->getDestination(),
      ]);
      $state->getOutput()->write($message, TRUE);
      $this->fileSystem->deleteRecursive($state->getDestination());
    }

    $destination = $state->getDestination();
    $this->fileSystem->prepareDirectory(
      $destination,
      FileSystemInterface::CREATE_DIRECTORY,
    );

    $message = (string) new FormattableMarkup('Created an empty destination directory (@dir).', [
      '@dir' => $state->getDestination(),
    ]);
    $state->getOutput()->write($message, TRUE);
  }

  /**
   * Exports blog entry contents.
   *
   * @param \Drupal\content_export\Data\ExportState $state
   *   The export state.
   */
  protected function exportBlogEntries(ExportState $state): void {
    $state->getOutput()->write('Starting export blog entries.', TRUE);
    $this->blogEntryExporter->exportMultiple($state);
  }

}
