<?php declare(strict_types = 1);

namespace Drupal\content_export\Writer;

use Drupal\content_export\Contract\MarkdownSourceInterface;
use Drupal\content_export\Data\BlogEntryExport;
use Drupal\content_export\Data\ExportState;
use Drupal\content_export\Data\MarkdownBuilderState;
use Drupal\content_export\Data\WriterState;
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
   * @param \Drupal\content_export\Data\ExportState $export_state
   *   The export state.
   */
  public function write(BlogEntryExport $export, ExportState $export_state): void {
    $destination_dir = $this->prepareDestinationDirectory($export, $export_state);
    $writer_state = new WriterState(
      $destination_dir,
      $export_state,
      new MarkdownBuilderState(),
    );
    $this->writeMarkdown($export, $writer_state);
    // @todo Write files tracked in MarkdownBuilderState.
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
    $working_dir = "$base_uri/blog/$id";
    $this->fileSystem->prepareDirectory(
      $working_dir,
      FileSystemInterface::CREATE_DIRECTORY,
    );

    return $working_dir;
  }

  /**
   * Writes markdown content.
   *
   * @param \Drupal\content_export\Data\BlogEntryExport $export
   *   The export data.
   * @param \Drupal\content_export\Data\WriterState $state
   *   The export state.
   */
  private function writeMarkdown(BlogEntryExport $export, WriterState $state): void {
    $langcode = $export->getFrontMatter()->getValue('language');
    $destination_file = "{$state->getWorkingDir()}/index.$langcode.md";

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
   * @param \Drupal\content_export\Data\WriterState $state
   *   The export state.
   *
   * @return string
   *   The front matter result.
   *
   * @throws \Exception
   */
  protected function buildFrontMatter(BlogEntryExport $export, WriterState $state): string {
    $front_matter = $export->getFrontMatter();

    return $this->markdownBuilderManager->buildMarkdown(
      $front_matter,
      $state->getMarkdownBuilderState(),
    );
  }

  /**
   * Builds content.
   *
   * @param \Drupal\content_export\Data\BlogEntryExport $export
   *   The export data.
   * @param \Drupal\content_export\Data\WriterState $state
   *   The state data.
   * @param array $content_parts
   *   An array with content parts.
   *
   * @throws \Exception
   */
  protected function buildContent(BlogEntryExport $export, WriterState $state, array &$content_parts): void {
    $content = $export->getContent();

    foreach ($content as $item) {
      \assert($item instanceof MarkdownSourceInterface);
      $content_parts[] = $this->markdownBuilderManager->buildMarkdown(
        $item,
        $state->getMarkdownBuilderState(),
      );
    }
  }

}
