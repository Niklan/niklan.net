<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Configuration;

/**
 * Represents an external content configuration.
 */
final class Configuration implements ConfigurationInterface {

  /**
   * The plugin ID.
   */
  protected string $id;

  /**
   * Constructs a new Configuration object.
   *
   * @param string $plugin_id
   *   The plugin ID.
   */
  public function __construct(string $plugin_id) {
    $this->id = $plugin_id;
  }

  /**
   * {@inheritdoc}
   */
  public function id(): string {
    return $this->id;
  }

}
