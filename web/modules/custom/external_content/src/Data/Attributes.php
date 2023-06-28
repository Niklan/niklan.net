<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Provides a value object for attributes.
 */
final class Attributes {

  /**
   * An array with attributes.
   */
  protected array $attributes = [];

  /**
   * Gets all attributes.
   */
  public function getAttributes(): array {
    return $this->attributes;
  }

  /**
   * Sets an attribute value.
   */
  public function setAttribute(string $attribute, string $value): self {
    $this->attributes[$attribute] = $value;

    return $this;
  }

  /**
   * Gets a value for an attribute.
   */
  public function getAttribute(string $attribute): ?string {
    return $this->attributes[$attribute] ?? NULL;
  }

  /**
   * Checks for attributes existence.
   */
  public function hasAttributes(): bool {
    return !!$this->attributes;
  }

  /**
   * Checks for specific attribute.
   */
  public function hasAttribute(string $attribute): bool {
    return \array_key_exists($attribute, $this->attributes);
  }

  /**
   * Removes assigned attribute.
   */
  public function removeAttribute(string $attribute): self {
    unset($this->attributes[$attribute]);

    return $this;
  }

}
