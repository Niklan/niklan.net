<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\SourceConfiguration;

/**
 * Provides an interface for source plugins.
 */
interface SourcePluginInterface {

  /**
   * The external content grouper plugin ID used by default.
   */
  public const DEFAULT_GROUPER_PLUGIN_ID = 'params';

  /**
   * Gets a plugin ID.
   *
   * @return string
   *   The plugin ID.
   */
  public function id(): string;

  /**
   * Gets a working dir.
   *
   * @return string
   *   The working dir.
   */
  public function workingDir(): string;

  /**
   * Gets a grouper plugin ID.
   *
   * @return string
   *   The external content grouper plugin ID.
   */
  public function grouperPluginId(): string;

  /**
   * Builds a source configuration DTO from the plugin.
   *
   * @return \Drupal\external_content\Data\SourceConfiguration
   *   The configuration instance.
   */
  public function toConfiguration(): SourceConfiguration;

}
