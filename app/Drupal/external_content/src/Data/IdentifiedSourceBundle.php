<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

final class IdentifiedSourceBundle {

  private array $sources = [];

  public function __construct(
    public readonly string $id,
  ) {}

  /**
   * @return \Drupal\external_content\Data\IdentifiedSource[]
   *   The identified sources.
   */
  public function sources(): array {
    return $this->sources;
  }

  public function getAllWithAttribute(string $attribute): self {
    $bundle = new self($this->id);

    foreach ($this->sources as $source) {
      \assert($source instanceof IdentifiedSource);

      if (!$source->attributes->hasAttribute($attribute)) {
        continue;
      }

      $bundle->add($source);
    }

    return $bundle;
  }

  public function add(IdentifiedSource $source): self {
    $this->sources[] = $source;

    return $this;
  }

  public function getAllWithAttributeValue(string $attribute, string $value): self {
    $bundle = new self($this->id);

    foreach ($this->sources as $source) {
      \assert($source instanceof IdentifiedSource);
      $attributes = $source->attributes;

      if (!$attributes->hasAttribute($attribute)) {
        continue;
      }

      if ($attributes->getAttribute($attribute) !== $value) {
        continue;
      }

      $bundle->add($source);
    }

    return $bundle;
  }

}
