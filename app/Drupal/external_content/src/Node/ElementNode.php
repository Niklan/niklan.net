<?php

declare(strict_types=1);

namespace Drupal\external_content\Node;

abstract class ElementNode extends ContentNode {

  /**
   * @param array{}|array<string, string> $properties
   */
  public function __construct(
    string $type,
    protected array $properties = [],
  ) {
    parent::__construct($type);
  }

  /**
   * @return array{}|array<string, string>
   */
  public function getProperties(): array {
    return $this->properties;
  }

  /**
   * @phpstan-assert-if-true string $this->getProperty()
   */
  public function hasProperty(string $property): bool {
    return \array_key_exists($property, $this->properties);
  }

  public function getProperty(string $property): string {
    if (!$this->hasProperty($property)) {
      throw new \LogicException('Trying to access property that does not exist.');
    }

    return $this->properties[$property];
  }

  public function setProperty(string $property, string $value): void {
    $this->properties[$property] = $value;
  }

  public function removeProperty(string $property): void {
    unset($this->properties[$property]);
  }

  /**
   * Based on that value renderers will decide how to process element.
   *
   * For example '<a>' element is inline, but '<p>' is a block container.
   */
  public function isInline(): bool {
    return FALSE;
  }

}
