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

  /**
   * Determines is this plugin available now or not.
   *
   * This can be helpful in cases when working dir is dynamic or can be unset.
   *
   * @return bool
   *   TRUE if plugin can be used, FALSE otherwise.
   */
  public function isActive(): bool;

}
