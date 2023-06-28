<?php declare(strict_types = 1);

namespace Drupal\external_content\Node;

/**
 * Represents a simple plain text inside elements.
 */
final class PlainText extends Node {

  /**
   * Constructs a new PlainText object.
   *
   * @param string $text
   *   The text content.
   */
  public function __construct(
    protected string $text,
  ) {}

  /**
   * Gets content.
   */
  public function getContent(): string {
    return $this->text;
  }

}
