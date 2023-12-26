<?php declare(strict_types = 1);

namespace Drupal\external_content\Dto;

/**
 * Represents a simple HTML element.
 */
final class HtmlElement extends Element {

  /**
   * Constructs a new HtmlElementParser object.
   *
   * @param string $tag
   *   The HTML tag name.
   * @param array $attributes
   *   The element attributes.
   */
  public function __construct(
    protected string $tag,
    protected array $attributes = [],
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

  /**
   * Sets attributes for element.
   *
   * @param array $attributes
   *   An array with element attributes keyed by attribute name.
   *
   * @return $this
   */
  public function setAttributes(array $attributes): self {
    $this->attributes = $attributes;

    return $this;
  }

  /**
   * Get element attributes.
   *
   * @return array
   *   An array with element attributes keyed by attribute name.
   */
  public function getAttributes(): array {
    return $this->attributes;
  }

  /**
   * Sets element attribute value.
   *
   * @param string $name
   *   The attribute name.
   * @param string $value
   *   The attribute value.
   *
   * @return $this
   */
  public function setAttribute(string $name, string $value): self {
    $this->attributes[$name] = $value;

    return $this;
  }

  /**
   * Gets element attribute value.
   *
   * @param string $name
   *   The attribute name.
   *
   * @return string|null
   *   The attribute value if exists.
   */
  public function getAttribute(string $name): ?string {
    return $this->hasAttribute($name) ? $this->attributes[$name] : NULL;
  }

  /**
   * Checks attribute for existence.
   *
   * @param string $name
   *   The attribute name.
   *
   * @return bool
   *   TRUE if attribute is exists on element, FALSE otherwise.
   */
  public function hasAttribute(string $name): bool {
    return \array_key_exists($name, $this->attributes);
  }

}
