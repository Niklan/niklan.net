<?php

declare(strict_types=1);

namespace Drupal\external_content_test\Dto;

use Drupal\external_content\Dto\ElementBase;

/**
 * Represents a testing element for HTML parser.
 */
final class TestElement extends ElementBase {

  /**
   * Constructs a new TestElement object.
   *
   * @param string $content
   *   The content value.
   */
  public function __construct(
    protected string $content,
  ) {}

  /**
   * Gets element content.
   *
   * @return string
   *   The element content.
   */
  public function getContent(): string {
    return $this->content;
  }

}
