<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Provides an external content bundle.
 */
final class SourceBundle implements \Countable, \IteratorAggregate {

  /**
   * The array with bundled documents.
   */
  protected array $documents = [];

  /**
   * Constructs a new ExternalContentBundle instance.
   *
   * @param string $id
   *   The bundle identifier.
   */
  public function __construct(
    protected string $id,
  ) {}

  /**
   * Gets the bundle identifier.
   */
  public function getId(): string {
    return $this->id;
  }

  /**
   * Gets bundle documents which has a specific attribute.
   */
  public function getByAttribute(string $attribute): self {
    $bundle = new self($this->id);

    $callback = static function (SourceVariant $source_variant) use ($bundle, $attribute): void {
      if (!$source_variant->attributes->hasAttribute($attribute)) {
        return;
      }

      $bundle->add($source_variant);
    };
    \array_walk($this->documents, $callback);

    return $bundle;
  }

  /**
   * Adds the document into bundle.
   */
  public function add(SourceVariant $document): self {
    $this->documents[] = $document;

    return $this;
  }

  /**
   * Gets bundle documents which has a specific attribute and value.
   */
  public function getByAttributeValue(string $attribute, string $value): self {
    $bundle = new self($this->id);

    $callback = static function (SourceVariant $source_variant) use ($bundle, $attribute, $value): void {
      $attributes = $source_variant->attributes;

      if (!$attributes->hasAttribute($attribute)) {
        return;
      }

      if ($attributes->getAttribute($attribute) !== $value) {
        return;
      }

      $bundle->add($source_variant);
    };
    \array_walk($this->documents, $callback);

    return $bundle;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->documents);
  }

  /**
   * {@inheritdoc}
   */
  public function count(): int {
    return \count($this->documents);
  }

}
