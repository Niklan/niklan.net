<?php

declare(strict_types=1);

namespace Drupal\external_content\DataStructure;

final class HtmlAttributes {

  public function __construct(
    private array $attributes = [],
  ) {}

  public function set(string $name, string $value): void {
    $this->attributes[$name] = $value;
  }

  public function get(string $name): ?string {
    return $this->attributes[$name] ?? NULL;
  }

  public function all(): array {
    return $this->attributes;
  }

  public function isEmpty(): bool {
    return empty($this->attributes);
  }

}
