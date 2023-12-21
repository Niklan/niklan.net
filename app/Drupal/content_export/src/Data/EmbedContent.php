<?php declare(strict_types = 1);

namespace Drupal\content_export\Data;

use Drupal\content_export\Contract\MarkdownSourceInterface;

/**
 * Represents an embed content.
 */
final class EmbedContent implements MarkdownSourceInterface {

  /**
   * Constructs a new EmbedContent instance.
   *
   * @param string $url
   *   The resource URL.
   */
  public function __construct(
    protected string $url,
  ) {}

  /**
   * Gets resource URL.
   *
   * @return string
   *   The resource URL.
   */
  public function getUrl(): string {
    return $this->url;
  }

}
