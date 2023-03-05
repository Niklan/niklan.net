<?php declare(strict_types = 1);

namespace Drupal\content_export\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drush\Commands\DrushCommands;

/**
 * Provides a content export commands.
 */
final class ContentExportCommands extends DrushCommands {

  /**
   * Constructs a new ContentExportCommands instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Exports the content from Paragraph entities into Markdown.
   *
   * @command niklan:content_export
   */
  public function contentExport(string $output_dir): void {
    // @todo Implement logic.
  }

}
