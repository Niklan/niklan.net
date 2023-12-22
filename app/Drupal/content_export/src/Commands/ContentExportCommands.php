<?php declare(strict_types = 1);

namespace Drupal\content_export\Commands;

use Drupal\content_export\Data\ExportState;
use Drupal\content_export\Exporter\ContentExporter;
use Drush\Commands\DrushCommands;

/**
 * Provides a content export commands.
 */
final class ContentExportCommands extends DrushCommands {

  /**
   * Constructs a new ContentExportCommands instance.
   *
   * @param \Drupal\content_export\Exporter\ContentExporter $exporter
   *   The content exporter.
   */
  public function __construct(
    protected ContentExporter $exporter,
  ) {}

  /**
   * Exports the content from Paragraph entities into Markdown.
   *
   * @command niklan:content_export
   */
  public function contentExport(string $output_dir = 'private://content'): void {
    $state = new ExportState($this->output(), $output_dir);
    $this->exporter->export($state);
  }

}
