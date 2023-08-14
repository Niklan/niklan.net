<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Provides an external content bundle.
 */
final class ExternalContentBundle implements \Countable, \IteratorAggregate {

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

    $callback = static function (ExternalContentBundleDocument $document) use ($bundle, $attribute): void {
      if (!$document->getAttributes()->hasAttribute($attribute)) {
        return;
      }

      $bundle->add($document);
    };
    \array_walk($this->documents, $callback);

    return $bundle;
  }

  /**
   * Adds the document into bundle.
   */
  public function add(ExternalContentBundleDocument $document): self {
    $this->documents[] = $document;

    return $this;
  }

  /**
   * Gets bundle documents which has a specific attribute and value.
   */
  public function getByAttributeValue(string $attribute, string $value): self {
    $bundle = new self($this->id);

    $callback = static function (ExternalContentBundleDocument $document) use ($bundle, $attribute, $value): void {
      $attributes = $document->getAttributes();

      if (!$attributes->hasAttribute($attribute)) {
        return;
      }

      if ($attributes->getAttribute($attribute) !== $value) {
        return;
      }

      $bundle->add($document);
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
