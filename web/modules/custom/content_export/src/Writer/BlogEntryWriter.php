<?php declare(strict_types = 1);

namespace Drupal\content_export\Writer;

use Drupal\content_export\Data\BlogEntryExport;
use Drupal\content_export\Data\ExportState;

/**
 * Provides a writer for 'blog_entry' content type.
 */
final class BlogEntryWriter {

  /**
   * Writes a single blog export.
   *
   * @param \Drupal\content_export\Data\BlogEntryExport $export
   *   The export data.
   * @param \Drupal\content_export\Data\ExportState $state
   *   The export state.
   */
  public function write(BlogEntryExport $export, ExportState $state): void {
    // @todo Write content.
  }

}
