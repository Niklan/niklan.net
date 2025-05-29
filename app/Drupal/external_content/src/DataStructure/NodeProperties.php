<?php

declare(strict_types=1);

namespace Drupal\external_content\DataStructure;

/**
 * A flat key/value storage for properties.
 *
 * Although node properties are very similar to attributes and share many common
 * features with them, they are not attributes. In the context of this module,
 * nodes represent content structure rather than HTML markup.
 */
final class NodeProperties {

  /**
   * @var array{}|array<string, string>
   */
  private array $properties = [];

  /**
   * @return array{}|array<string, string>
   */
  public function all(): array {
    return $this->properties;
  }

  /**
   * @phpstan-assert-if-true string $this->getProperty()
   */
  public function hasProperty(string $property): bool {
    return \array_key_exists($property, $this->properties);
  }

  /**
   * @throws \OutOfBoundsException
   */
  public function getProperty(string $property): string {
    if (!$this->hasProperty($property)) {
      throw new \OutOfBoundsException("Property '{$property}' not found.");
    }
    return $this->properties[$property];
  }

  public function setProperty(string $property, string $value): void {
    if ($property === '' || $value === '') {
      throw new \InvalidArgumentException("Property key and value shouldn't be an empty string");
    }
    $this->properties[$property] = $value;
  }

  public function removeProperty(string $property): void {
    unset($this->properties[$property]);
  }

}
