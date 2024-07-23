<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

/**
 * Provides a simple data storage to pass through the process.
 */
final class Data {

  public function __construct(
    protected array $store = [],
  ) {}

  /**
   * Sets the data value.
   *
   * @param string $key
   *   The data key.
   * @param mixed $value
   *   The value to store.
   */
  public function set(string $key, mixed $value): self {
    $this->store[$key] = $value;

    return $this;
  }

  /**
   * Gets the value for a key.
   *
   * @param string $key
   *   The data key.
   */
  public function get(string $key): mixed {
    if (!$this->has($key)) {
      return NULL;
    }

    return $this->store[$key];
  }

  /**
   * Checks data for existence.
   *
   * @param string $key
   *   The data key.
   */
  public function has(string $key): bool {
    return \array_key_exists($key, $this->store);
  }

  public function all(): array {
    return $this->store;
  }

}
