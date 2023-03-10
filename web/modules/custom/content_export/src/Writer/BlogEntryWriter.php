<?php declare(strict_types = 1);

namespace Drupal\content_export\Writer;

use Drupal\content_export\Contract\MarkdownSourceInterface;
use Drupal\content_export\Data\BlogEntryExport;
use Drupal\content_export\Data\ExportState;
use Drupal\content_export\Manager\MarkdownBuilderManager;
use Drupal\Core\File\FileSystemInterface;

/**
 * Provides a writer for 'blog_entry' content type.
 */
final class BlogEntryWriter {

  /**
   * Constructs a new BlogEntryWriter instance.
   *
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   The file system.
   * @param \Drupal\content_export\Manager\MarkdownBuilderManager $markdownBuilderManager
   *   The Markdown builder manager.
   */
  public function __construct(
    protected FileSystemInterface $fileSystem,
    protected MarkdownBuilderManager $markdownBuilderManager,
  ) {}

  /**
   * Writes a single blog export.
   *
   * @param \Drupal\content_export\Data\BlogEntryExport $export
   *   The export data.
   * @param \Drupal\content_export\Data\ExportState $state
   *   The export state.
   */
  public function write(BlogEntryExport $export, ExportState $state): void {
    $destination_dir = $this->prepareDestinationDirectory($export, $state);
    $this->writeMarkdown($destination_dir, $export, $state);
  }

  /**
   * Prepares destination directory.
   *
   * @param \Drupal\content_export\Data\BlogEntryExport $export
   *   The export data.
   * @param \Drupal\content_export\Data\ExportState $state
   *   The export state.
   *
   * @return string
   *   The destination URI.
   */
  protected function prepareDestinationDirectory(BlogEntryExport $export, ExportState $state): string {
    $base_uri = $state->getDestination();
    $id = $export->getFrontMatter()->getValue('id');
    $destination_dir = "$base_uri/blog/$id";
    $this->fileSystem->prepareDirectory(
      $destination_dir,
      FileSystemInterface::CREATE_DIRECTORY,
    );

    return $destination_dir;
  }

  /**
   * Writes markdown content.
   *
   * @param string $destination_dir
   *   The destination directory.
   * @param \Drupal\content_export\Data\BlogEntryExport $export
   *   The export data.
   * @param \Drupal\content_export\Data\ExportState $state
   *   The export state.
   */
  private function writeMarkdown(string $destination_dir, BlogEntryExport $export, ExportState $state): void {
    $langcode = $export->getFrontMatter()->getValue('language');
    $destination_file = "$destination_dir/index.$langcode.md";

    $content_parts = [];
    $content_parts[] = $this->buildFrontMatter($export, $state);
    $this->buildContent($export, $state, $content_parts);

    $content = \implode(\PHP_EOL . \PHP_EOL, $content_parts);
    // Make sure content have empty line at the end.
    $content .= \PHP_EOL;

    $this->fileSystem->saveData(
      $content,
      $destination_file,
      FileSystemInterface::EXISTS_REPLACE,
    );
  }

  /**
   * Builds a Front Matter contents.
   *
   * @param \Drupal\content_export\Data\BlogEntryExport $export
   *   The export data.
   * @param \Drupal\content_export\Data\ExportState $state
   *   The export state.
   */
  protected function buildFrontMatter(BlogEntryExport $export, ExportState $state): string {
    $front_matter = $export->getFrontMatter();

    return $this->markdownBuilderManager->buildMarkdown($front_matter);
  }

  /**
   * Builds content.
   *
   * @param \Drupal\content_export\Data\BlogEntryExport $export
   *   The export data.
   * @param \Drupal\content_export\Data\ExportState $state
   *   The state data.
   * @param array $content_parts
   *   An array with content parts.
   */
  protected function buildContent(BlogEntryExport $export, ExportState $state, array &$content_parts): void {
    $content = $export->getContent();

    foreach ($content as $item) {
      \assert($item instanceof MarkdownSourceInterface);
      $content_parts[] = $this->markdownBuilderManager->buildMarkdown($item);
    }
  }

}
