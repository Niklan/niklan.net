<?php declare(strict_types = 1);

namespace Drupal\content_export\Contract;

/**
 * Defines an interface for Markdown builders.
 */
interface MarkdownBuilderInterface {

  /**
   * Checks whether this builder supports source data.
   *
   * @param \Drupal\content_export\Contract\MarkdownSourceInterface $source
   *   The source.
   *
   * @return bool
   *   TRUE if supports it, FALSE otherwise.
   */
  public static function isApplicable(MarkdownSourceInterface $source): bool;

  /**
   * Builds a Markdown content.
   *
   * @param \Drupal\content_export\Contract\MarkdownSourceInterface $source
   *   The Markdown source.
   *
   * @return string
   *   The Markdown content.
   */
  public function build(MarkdownSourceInterface $source): string;

}
