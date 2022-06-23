<?php

declare(strict_types=1);

namespace Drupal\external_content\Dto;

/**
 * Represents a simple HTML element.
 */
final class HtmlElement extends ElementBase {

  /**
   * Constructs a new HtmlElementParser object.
   *
   * @param string $tag
   *   The HTML tag name.
   */
  public function __construct(
    protected string $tag,
  ) {}

  /**
   * Gets tag name.
   *
   * @return string
   *   The HTML tag name.
   */
  public function getTag(): string {
    return $this->tag;
  }

}
