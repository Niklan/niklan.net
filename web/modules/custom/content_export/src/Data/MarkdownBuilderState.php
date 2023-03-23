<?php declare(strict_types = 1);

namespace Drupal\content_export\Data;

/**
 * Provides a state storage for markdown builder.
 */
final class MarkdownBuilderState {

  /**
   * An array with file URIs tracked during build.
   */
  protected array $fileUris = [];

  /**
   * Tracks file URI.
   *
   * @param string $uri
   *   The file URI.
   *
   * @return $this
   */
  public function trackFileUri(string $uri): self {
    if (!\in_array($uri, $this->fileUris)) {
      $this->fileUris[] = $uri;
    }

    return $this;
  }

  /**
   * Gets tracked file URIs.
   *
   * @return array
   *   An array with tracked file URIs.
   */
  public function getTrackedFileUris(): array {
    return $this->fileUris;
  }

}
