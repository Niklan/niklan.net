<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Node\ExternalContentDocument;

/**
 * Provides a value object for a single document inside bundle.
 */
final class ExternalContentBundleDocument {

  /**
   * Constructs a new ExternalContentBundleDocument instance.
   *
   * @param \Drupal\external_content\Node\ExternalContentDocument $document
   *   The external content document.
   * @param array $attributes
   *   The attributes (traits) associated by this document inside a bundle.
   */
  public function __construct(
    protected ExternalContentDocument $document,
    protected array $attributes = [],
  ) {}

  /**
   * Gets the document instance.
   */
  public function getDocument(): ExternalContentDocument {
    return $this->document;
  }

  /**
   * Gets the attributes associated with a document within bundle.
   */
  public function getAttributes(): array {
    return $this->attributes;
  }

  /**
   * Assign an attribute for that document in the bundle.
   */
  public function setAttribute(string $attribute, string $value): self {
    $this->attributes[$attribute] = $value;

    return $this;
  }

  /**
   * Gets the attribute value.
   */
  public function getAttribute(string $attribute): ?string {
    return $this->attributes[$attribute] ?? NULL;
  }

  /**
   * Checks for attributes existence for that document.
   */
  public function hasAttributes(): bool {
    return !!$this->attributes;
  }

  /**
   * Checks for specific attribute assigned to document.
   */
  public function hasAttribute(string $attribute): bool {
    return \array_key_exists($attribute, $this->attributes);
  }

  /**
   * Removes assigned attribute.
   */
  public function removeAttribute(string $attribute): self {
    \unset($this->attributes[$attribute]);

    return $this;
  }

}
