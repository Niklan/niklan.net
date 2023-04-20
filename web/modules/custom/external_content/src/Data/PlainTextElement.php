<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

/**
 * Represents a plain text.
 */
final class PlainTextElement extends Element {

  /**
   * Constructs a new PlainTextElement object.
   *
   * @param string $text
   *   The text content.
   */
  public function __construct(
    protected string $text,
  ) {}

  /**
   * Gets content.
   *
   * @return string
   *   The text content.
   */
  public function getContent(): string {
    return $this->text;
  }

}
