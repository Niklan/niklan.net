<?php declare(strict_types = 1);

namespace Drupal\content_export\Builder;

use Drupal\Component\Serialization\Yaml;
use Drupal\content_export\Contract\MarkdownBuilderInterface;
use Drupal\content_export\Contract\MarkdownSourceInterface;
use Drupal\content_export\Data\FrontMatter;

/**
 * Provides a Markdown builder for Front Matter.
 */
final class FrontMatterMarkdownBuilder implements MarkdownBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(MarkdownSourceInterface $source): bool {
    return $source instanceof FrontMatter;
  }

  /**
   * {@inheritdoc}
   */
  public function build(MarkdownSourceInterface $source): string {
    \assert($source instanceof FrontMatter);

    $markdown = '---';
    $markdown .= \PHP_EOL;
    $markdown .= Yaml::encode($source->getValues());
    $markdown .= '---';

    return $markdown;
  }

}
