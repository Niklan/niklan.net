<?php declare(strict_types = 1);

namespace Drupal\content_export\Data;

use Drupal\content_export\Contract\MarkdownSourceInterface;

/**
 * Represents a text content.
 */
final class TextContent implements MarkdownSourceInterface {

  /**
   * Constructs a new TextContent instance.
   *
   * @param string $text
   *   The text value.
   */
  public function __construct(
    protected string $text,
  ) {}

  /**
   * Gets the text.
   */
  public function getText(): string {
    return $this->text;
  }

}
