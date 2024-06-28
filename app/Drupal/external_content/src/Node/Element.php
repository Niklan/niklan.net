<?php

declare(strict_types=1);

namespace Drupal\external_content\Node;

use Drupal\external_content\Data\Attributes;

/**
 * Represents a simple HTML element.
 */
final class Element extends Node {

  /**
   * Constructs a new HtmlElementParser object.
   *
   * @param string $tag
   *   The HTML tag name.
   * @param \Drupal\external_content\Data\Attributes|null $attributes
   *   The element attributes.
   */
  public function __construct(
    protected string $tag,
    protected ?Attributes $attributes = NULL,
  ) {
    $this->attributes ??= new Attributes();
  }

  /**
   * Gets tag name.
   */
  public function getTag(): string {
    return $this->tag;
  }

  /**
   * Get element attributes.
   */
  public function getAttributes(): Attributes {
    return $this->attributes;
  }

}
