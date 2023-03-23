<?php declare(strict_types = 1);

namespace Drupal\content_export\Builder;

use Drupal\content_export\Contract\MarkdownBuilderInterface;
use Drupal\content_export\Contract\MarkdownSourceInterface;
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
  public function build(MarkdownSourceInterface $source): string {
    \assert($source instanceof VideoContent);

    // @todo Replace URI with relative path and store it somewhere. Most likely
    //   it require to add new parameters - BuildContext and store them there.
    return "![{$source->getAlt()}]({$source->getUri()})";
  }

}
