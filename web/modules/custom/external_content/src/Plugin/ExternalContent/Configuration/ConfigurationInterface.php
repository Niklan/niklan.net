<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Configuration;

/**
 * Provides an interface for external content settings.
 */
interface ConfigurationInterface {

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

}
