<?php declare(strict_types = 1);

namespace Drupal\content_export\Contract;

/**
 * Defines a Markdown source.
 */
interface MarkdownSourceInterface {

  /**
   * Gets source for Markdown.
   *
   * @return mixed
   *   The source value.
   */
  public function getMarkdownSource(): mixed;

}
