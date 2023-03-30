<?php declare(strict_types = 1);

namespace Drupal\content_export\Data;

use Drupal\content_export\Contract\MarkdownSourceInterface;

/**
 * Represents a video content.
 */
final class VideoContent implements MarkdownSourceInterface {

  /**
   * Constructs a new videoContent instance.
   *
   * @param string $uri
   *   The video URI.
   * @param string $alt
   *   The video alt.
   */
  public function __construct(
    protected string $uri,
    protected string $alt,
  ) {}

  /**
   * Gets video URI.
   *
   * @return string
   *   The video URI.
   */
  public function getUri(): string {
    return $this->uri;
  }

  /**
   * Gets video alt.
   *
   * @return string
   *   The video alt.
   */
  public function getAlt(): string {
    return $this->alt;
  }

}
