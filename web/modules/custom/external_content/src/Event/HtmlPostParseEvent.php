<?php declare(strict_types = 1);

namespace Drupal\external_content\Event;

use Drupal\external_content\Node\Content;

/**
 * Provides an event after HTML is parsed into AST.
 */
final class HtmlPostParseEvent extends Event {

  /**
   * Constructs a new HtmlPreParseEvent instance.
   */
  public function __construct(
    protected Content $document,
  ) {}

  /**
   * Gets the external content document.
   */
  public function getHtml(): Content {
    return $this->document;
  }

}
