<?php declare(strict_types = 1);

namespace Drupal\content_export\Builder;

use Drupal\Component\Serialization\Yaml;
use Drupal\content_export\Contract\MarkdownBuilderInterface;
use Drupal\content_export\Contract\MarkdownSourceInterface;
use Drupal\content_export\Data\FrontMatter;
use Drupal\content_export\Data\MarkdownBuilderState;

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
  public function build(MarkdownSourceInterface $source, MarkdownBuilderState $state): string {
    \assert($source instanceof FrontMatter);

    $values = $source->getValues();

    $markdown = '---';
    $markdown .= \PHP_EOL;
    $markdown .= Yaml::encode($values);
    $markdown .= '---';

    if (\array_key_exists('attachments', $values)) {
      foreach ($values['attachments'] as $attachment) {
        $state->trackFileUri($attachment['path']);
      }
    }

    if (\array_key_exists('promo', $values)) {
      $state->trackFileUri($values['promo']);
    }

    return $markdown;
  }

}
