<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

final class ContentBundle implements \Countable, \IteratorAggregate {

  protected array $items = [];

  /**
   * Constructs a new ContentBundle instance.
   */
  public function __construct(
    public readonly string $id,
  ) {}

  public function getByAttribute(string $attribute): self {
    $bundle = new self($this->id);

    $callback = static function (ContentVariation $variation) use ($bundle, $attribute): void {
      if (!$variation->attributes->hasAttribute($attribute)) {
        return;
      }

      $bundle->add($variation);
    };
    \array_walk($this->items, $callback);

    return $bundle;
  }

  public function add(ContentVariation $variation): self {
    $this->items[] = $variation;

    return $this;
  }

  public function getByAttributeValue(string $attribute, string $value): self {
    $bundle = new self($this->id);

    $callback = static function (ContentVariation $variation) use ($bundle, $attribute, $value): void {
      $attributes = $variation->attributes;

      if (!$attributes->hasAttribute($attribute)) {
        return;
      }

      if ($attributes->getAttribute($attribute) !== $value) {
        return;
      }

      $bundle->add($variation);
    };
    \array_walk($this->items, $callback);

    return $bundle;
  }

  #[\Override]
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->items);
  }

  #[\Override]
  public function count(): int {
    return \count($this->items);
  }

}
