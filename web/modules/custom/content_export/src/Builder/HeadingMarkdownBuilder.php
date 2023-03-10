<?php declare(strict_types = 1);

namespace Drupal\content_export\Builder;

use Drupal\content_export\Contract\MarkdownBuilderInterface;
use Drupal\content_export\Contract\MarkdownSourceInterface;
use Drupal\content_export\Data\HeadingContent;

/**
 * Provides a Markdown builder for heading.
 */
final class HeadingMarkdownBuilder implements MarkdownBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(MarkdownSourceInterface $source): bool {
    return $source instanceof HeadingContent;
  }

  /**
   * {@inheritdoc}
   */
  public function build(MarkdownSourceInterface $source): string {
    \assert($source instanceof HeadingContent);

    $markdown = \str_repeat('#', $source->getLevel());
    $markdown .= ' ';
    $markdown .= $source->getHeading();

    return $markdown;
  }

}
