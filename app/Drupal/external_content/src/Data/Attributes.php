<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

/**
 * Provides a value object for attributes.
 */
final class Attributes {

  /**
   * Constructs a new Attributes instance.
   */
  public function __construct(
    protected array $attributes = [],
  ) {}

  /**
   * Gets all attributes.
   */
  public function all(): array {
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
  public function getAttribute(string $attribute): string {
    if (!isset($this->attributes[$attribute])) {
      throw new \OutOfBoundsException(\sprintf('The offset "%s" does not exist.', $attribute));
    }

    return $this->attributes[$attribute];
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
