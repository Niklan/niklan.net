<?php declare(strict_types = 1);

namespace Drupal\content_export\Data;

use Drupal\content_export\Contract\MarkdownSourceInterface;

/**
 * Represents an image content.
 */
final class ImageContent implements MarkdownSourceInterface {

  /**
   * Constructs a new ImageContent instance.
   *
   * @param string $uri
   *   The image URI.
   * @param string $alt
   *   The image alt.
   */
  public function __construct(
    protected string $uri,
    protected string $alt,
  ) {}

  /**
   * Gets image URI.
   */
  public function getUri(): string {
    return $this->uri;
  }

  /**
   * Gets image alt.
   */
  public function getAlt(): string {
    return $this->alt;
  }

}
