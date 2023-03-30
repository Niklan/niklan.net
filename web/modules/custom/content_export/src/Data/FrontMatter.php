<?php declare(strict_types = 1);

namespace Drupal\content_export\Data;

use Drupal\content_export\Contract\MarkdownSourceInterface;

/**
 * Provides a data object for Front Matter.
 */
final class FrontMatter implements MarkdownSourceInterface {

  /**
   * Constructs a new FrontMatter instance.
   *
   * @param array|null $values
   *   The Front Matter Values.
   */
  public function __construct(
    protected ?array $values = [],
  ) {}

  /**
   * Gets values.
   *
   * @return array
   *   An array with values.
   */
  public function getValues(): array {
    return $this->values;
  }

  /**
   * Gets the specific value.
   *
   * @param string $key
   *   The value key.
   * @param mixed|NULL $default
   *   The default value if it's missing by key.
   *
   * @return mixed
   *   The result.
   */
  public function getValue(string $key, mixed $default = NULL): mixed {
    return $this->values[$key] ?? $default;
  }

}
