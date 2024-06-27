<?php

declare(strict_types=1);

namespace Drupal\external_content\Data;

/**
 * {@selfdoc}
 */
final class ContentBundle implements \Countable, \IteratorAggregate {

  /**
   * {@selfdoc}
   */
  protected array $items = [];

  /**
   * Constructs a new ContentBundle instance.
   */
  public function __construct(
    public readonly string $id,
  ) {}

  /**
   * {@selfdoc}
   */
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

  /**
   * {@selfdoc}
   */
  public function add(ContentVariation $variation): self {
    $this->items[] = $variation;

    return $this;
  }

  /**
   * {@selfdoc}
   */
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

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->items);
  }

  /**
   * {@inheritdoc}
   */
  public function count(): int {
    return \count($this->items);
  }

}
