<?php declare(strict_types = 1);

namespace Drupal\external_content\Event;

use Drupal\external_content\Node\ExternalContentDocument;

/**
 * Provides an event after HTML is parsed into AST.
 */
final class HtmlPostParseEvent extends Event {

  /**
   * Constructs a new HtmlPreParseEvent instance.
   */
  public function __construct(
    protected ExternalContentDocument $document,
  ) {}

  /**
   * Gets the external content document.
   */
  public function getHtml(): ExternalContentDocument {
    return $this->document;
  }

}
