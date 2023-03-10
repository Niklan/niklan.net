<?php declare(strict_types = 1);

namespace Drupal\content_export\Builder;

use Drupal\content_export\Contract\MarkdownBuilderInterface;
use Drupal\content_export\Contract\MarkdownSourceInterface;
use Drupal\content_export\Data\TextContent;

/**
 * Provides a Markdown builder for text.
 */
final class TextMarkdownBuilder implements MarkdownBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(MarkdownSourceInterface $source): bool {
    return $source instanceof TextContent;
  }

  /**
   * {@inheritdoc}
   */
  public function build(MarkdownSourceInterface $source): string {
    \assert($source instanceof TextContent);

    return $source->getText();
  }

}
