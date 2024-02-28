<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * {@selfdoc}
 */
final readonly class IdentifiedSourceBundle {

  /**
   * {@selfdoc}
   */
  private \SplObjectStorage $sources;

  /**
   * {@selfdoc}
   */
  public function __construct(
    public string $id,
  ) {
    $this->sources = new \SplObjectStorage();
  }

  /**
   * {@selfdoc}
   */
  public function add(IdentifiedSource $source): self {
    $this->sources->attach($source);

    return $this;
  }

  /**
   * {@selfdoc}
   */
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

  /**
   * {@selfdoc}
   */
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
