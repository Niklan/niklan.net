<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Represents a configuration for external content source.
 */
final class SourceConfiguration {

  /**
   * Constructs a new SourceConfiguration instance.
   *
   * @param string $workingDir
   *   The working dir with a source content.
   * @param string $groupingPluginId
   *   The grouper plugin ID should be used for grouping this content.
   * @param string|null $sourcePluginId
   *   The source plugin ID which provided that configuration if any. This can
   *   be NULL for some use cases where content can be build without involving
   *   source plugins.
   */
  public function __construct(
    protected string $workingDir,
    protected string $groupingPluginId,
    protected ?string $sourcePluginId = NULL,
  ) {}

  /**
   * Gets the working dir.
   *
   * @return string
   *   The working dir path.
   */
  public function getWorkingDir(): string {
    return $this->workingDir;
  }

  /**
   * Gets the grouping plugin ID.
   *
   * @return string
   *   The grouping plugin ID.
   */
  public function getGroupingPluginId(): string {
    return $this->groupingPluginId;
  }

  /**
   * Gets the source plugin ID.
   *
   * @return string|null
   *   The source plugin ID if configuration build from plugin.
   */
  public function getSourcePluginId(): ?string {
    return $this->sourcePluginId;
  }

}
