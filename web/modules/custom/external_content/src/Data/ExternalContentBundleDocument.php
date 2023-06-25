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
   * @param \Drupal\external_content\Data\Attributes|null $attributes
   *   The attributes (traits) associated by this document inside a bundle.
   */
  public function __construct(
    protected ExternalContentDocument $document,
    protected ?Attributes $attributes = NULL,
  ) {
    if ($this->attributes) {
      return;
    }

    $this->attributes = new Attributes();
  }

  /**
   * Gets the document instance.
   */
  public function getDocument(): ExternalContentDocument {
    return $this->document;
  }

  /**
   * Gets the document attributes.
   */
  public function getAttributes(): Attributes {
    return $this->attributes;
  }

}
