<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Provides a configuration store.
 */
final class Configuration {

  public function __construct(
    protected array $configuration = [],
  ) {}

  public function get(string $key): mixed {
    if (!$this->exists($key)) {
      return NULL;
    }

    return $this->configuration[$key];
  }

  public function exists(string $key): bool {
    return \array_key_exists($key, $this->configuration);
  }

}
