<?php declare(strict_types = 1);

namespace Drupal\content_export\Builder;

use Drupal\content_export\Contract\MarkdownBuilderInterface;
use Drupal\content_export\Contract\MarkdownSourceInterface;
use Drupal\content_export\Data\ImageContent;

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
  public function build(MarkdownSourceInterface $source): string {
    \assert($source instanceof ImageContent);

    // @todo Replace URI with relative path and store it somewhere. Most likely
    //   it require to add new parameters - BuildContext and store them there.
    return "![{$source->getAlt()}]({$source->getUri()})";
  }

}
