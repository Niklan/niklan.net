<?php declare(strict_types = 1);

namespace Drupal\content_export\Contract;

use Drupal\content_export\Data\MarkdownBuilderState;

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
   * @param \Drupal\content_export\Data\MarkdownBuilderState $state
   *   The export state.
   *
   * @return string
   *   The Markdown content.
   */
  public function build(MarkdownSourceInterface $source, MarkdownBuilderState $state): string;

}
