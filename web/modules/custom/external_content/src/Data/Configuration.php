<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Provides a configuration store.
 */
final class Configuration {

  /**
   * Constructs a new Configuration instance.
   *
   * @param array $configuration
   *   The configurations.
   */
  public function __construct(
    protected array $configuration = [],
  ) {}

  /**
   * Gets the configuration value by a key.
   *
   * @param string $key
   *   The key.
   *
   * @return mixed
   *   The value, if exists.
   */
  public function get(string $key): mixed {
    if (!$this->exists($key)) {
      return NULL;
    }

    return $this->configuration[$key];
  }

  /**
   * Checks configuration for existence.
   *
   * @param string $key
   *   The configuration key.
   *
   * @return bool
   *   TRUE if value exists, FALSE if not.
   */
  public function exists(string $key): bool {
    return \array_key_exists($key, $this->configuration);
  }

}
