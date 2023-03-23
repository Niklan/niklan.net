<?php declare(strict_types = 1);

namespace Drupal\content_export\Builder;

use Drupal\content_export\Contract\MarkdownBuilderInterface;
use Drupal\content_export\Contract\MarkdownSourceInterface;
use Drupal\content_export\Data\MarkdownBuilderState;
use Drupal\content_export\Data\VideoContent;

/**
 * Provides a Markdown builder for video.
 */
final class VideoMarkdownBuilder implements MarkdownBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(MarkdownSourceInterface $source): bool {
    return $source instanceof VideoContent;
  }

  /**
   * {@inheritdoc}
   */
  public function build(MarkdownSourceInterface $source, MarkdownBuilderState $state): string {
    \assert($source instanceof VideoContent);
    $state->trackFileUri($source->getUri());

    return "![{$source->getAlt()}]({$source->getUri()})";
  }

}
