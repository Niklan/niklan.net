<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Node\ExternalContentDocument;

/**
 * Provides an external content bundle.
 */
final class ExternalContentBundle {

  /**
   * The array with bundled documents.
   */
  protected array $documents = [];

  /**
   * Adds the document into bundle.
   */
  public function add(ExternalContentDocument $document): self {
    $this->documents[] = $document;

    return $this;
  }

}
