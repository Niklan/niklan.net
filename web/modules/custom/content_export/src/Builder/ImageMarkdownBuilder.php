<?php declare(strict_types = 1);

namespace Drupal\content_export\Builder;

use Drupal\content_export\Contract\MarkdownBuilderInterface;
use Drupal\content_export\Contract\MarkdownSourceInterface;
use Drupal\content_export\Data\ImageContent;
use Drupal\content_export\Data\MarkdownBuilderState;

/**
 * Provides a Markdown builder for image.
 */
final class ImageMarkdownBuilder implements MarkdownBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(MarkdownSourceInterface $source): bool {
    return $source instanceof ImageContent;
  }

  /**
   * {@inheritdoc}
   */
  public function build(MarkdownSourceInterface $source, MarkdownBuilderState $state): string {
    \assert($source instanceof ImageContent);
    $state->trackFileUri($source->getUri());

    return "![{$source->getAlt()}]({$source->getUri()})";
  }

}
