<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Provides an external content bundle.
 */
final class SourceBundle implements \Countable, \IteratorAggregate {

  /**
   * {@selfdoc}
   */
  protected array $items = [];

  /**
   * Constructs a new SourceBundle instance.
   */
  public function __construct(
    public readonly string $id,
  ) {}

  /**
   * {@selfdoc}
   */
  public function getByAttribute(string $attribute): self {
    $bundle = new self($this->id);

    $callback = static function (SourceVariation $variation) use ($bundle, $attribute): void {
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
  public function add(SourceVariation $variation): self {
    $this->items[] = $variation;

    return $this;
  }

  /**
   * {@selfdoc}
   */
  public function getByAttributeValue(string $attribute, string $value): self {
    $bundle = new self($this->id);

    $callback = static function (SourceVariation $source_variant) use ($bundle, $attribute, $value): void {
      $attributes = $source_variant->attributes;

      if (!$attributes->hasAttribute($attribute)) {
        return;
      }

      if ($attributes->getAttribute($attribute) !== $value) {
        return;
      }

      $bundle->add($source_variant);
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
