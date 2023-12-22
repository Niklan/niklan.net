<?php declare(strict_types = 1);

namespace Drupal\external_content\Event;

use Drupal\external_content\Data\Data;

/**
 * {@selfdoc}
 */
final class HtmlPreParseEvent extends Event {

  /**
   * Constructs a new HtmlPreParseEvent instance.
   */
  public function __construct(
    public string $content,
    public Data $data,
  ) {}

}
