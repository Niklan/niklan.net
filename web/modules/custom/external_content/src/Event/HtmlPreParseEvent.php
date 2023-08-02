<?php declare(strict_types = 1);

namespace Drupal\external_content\Event;

use Drupal\external_content\Data\ExternalContentHtml;

/**
 * Provides an event before HTML is going to be parsed.
 */
final class HtmlPreParseEvent extends Event {

  /**
   * Constructs a new HtmlPreParseEvent instance.
   */
  public function __construct(
    protected ExternalContentHtml $html,
  ) {}

  /**
   * Gets the external content HTML.
   */
  public function getHtml(): ExternalContentHtml {
    return $this->html;
  }

}
