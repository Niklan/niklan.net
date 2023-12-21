<?php declare(strict_types = 1);

namespace Drupal\content_export\Data;

/**
 * Provides a state class for write operation.
 */
final class WriterState {

  /**
   * Constructs a new WriterState instance.
   *
   * @param string $workingDir
   *   The current working dir.
   * @param \Drupal\content_export\Data\ExportState $exportState
   *   The export state.
   * @param \Drupal\content_export\Data\MarkdownBuilderState $markdownBuilderState
   *   The markdown builder state.
   */
  public function __construct(protected string $workingDir, protected ExportState $exportState, protected MarkdownBuilderState $markdownBuilderState,) {}

  /**
   * Sets the working dir.
   *
   * @param string $working_dir
   *   The working dir.
   *
   * @return $this
   */
  public function setWorkingDir(string $working_dir): self {
    $this->workingDir = $working_dir;

    return $this;
  }

  /**
   * Gets the working dir.
   *
   * @return string
   *   The working dir.
   */
  public function getWorkingDir(): string {
    return $this->workingDir;
  }

  /**
   * Gets the export state.
   *
   * @return \Drupal\content_export\Data\ExportState
   *   The export state.
   */
  public function getExportState(): ExportState {
    return $this->exportState;
  }

  /**
   * Gets markdown builder state.
   *
   * @return \Drupal\content_export\Data\MarkdownBuilderState
   *   The markdown builder state.
   */
  public function getMarkdownBuilderState(): MarkdownBuilderState {
    return $this->markdownBuilderState;
  }

}
