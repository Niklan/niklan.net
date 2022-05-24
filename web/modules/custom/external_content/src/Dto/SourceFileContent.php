<?php

declare(strict_types=1);

namespace Drupal\external_content\Dto;

/**
 * Represents source file unprocessed content (cleaned from FrontMatter).
 */
final class SourceFileContent {

  /**
   * Constructs a new SourceFileContent object.
   *
   * @param string $content
   *   The source content.
   */
  public function __construct(
    protected string $content,
  ) {}

  /**
   * Gets content.
   *
   * @return string
   *   The content.
   */
  public function value(): string {
    return $this->content;
  }

}
