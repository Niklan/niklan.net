<?php declare(strict_types = 1);

namespace Drupal\content_export\Data;

/**
 * Provides a data object for Front Matter.
 */
final class FrontMatter {

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

}
