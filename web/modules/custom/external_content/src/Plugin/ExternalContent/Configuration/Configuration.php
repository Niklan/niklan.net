<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Configuration;

use Drupal\migrate\Plugin\Exception\BadPluginDefinitionException;

/**
 * Represents an external content configuration.
 *
 * The configuration is an YAML plugin defined in a file *.external_content.yml
 * and contains:
 * - id: The configuration ID. The configuration key is an ID.
 * - working_dir: The local URI where to search for external content.
 * - grouper: (optional) The external content grouper plugin ID.
 * - loader: The loader plugin ID which will be used for loading external
 *   content.
 *
 * @todo Replace by source plugin system.
 */
final class Configuration implements ConfigurationInterface {

  /**
   * The plugin ID.
   */
  protected string $id;

  /**
   * The working directory.
   */
  protected string $workingDir;

  /**
   * The external content grouper plugin.
   */
  protected string $grouper;

  /**
   * Constructs a new Configuration object.
   *
   * @param string $plugin_id
   *   The plugin ID.
   * @param array $plugin_definition
   *   An array with plugin definition.
   *
   * @throws \Drupal\migrate\Plugin\Exception\BadPluginDefinitionException
   *   Whe some mandatory property is missing.
   */
  public function __construct(string $plugin_id, array $plugin_definition) {
    $this->id = $plugin_id;

    if (!\array_key_exists('working_dir', $plugin_definition)) {
      throw new BadPluginDefinitionException($plugin_id, 'working_dir');
    }

    $this->workingDir = $plugin_definition['working_dir'];
    $this->grouper = $plugin_definition['grouper'] ?? self::DEFAULT_GROUPER_PLUGIN_ID;
  }

  /**
   * {@inheritdoc}
   */
  public function id(): string {
    return $this->id;
  }

  /**
   * Gets a working dir.
   *
   * @return string
   *   The working dir.
   */
  public function workingDir(): string {
    return $this->workingDir;
  }

  /**
   * {@inheritdoc}
   */
  public function grouperPluginId(): string {
    return $this->grouper;
  }

}
