<?php

declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Markup;

use Drupal\Core\Plugin\PluginBase;

/**
 * Represents a simple plain text without markup.
 *
 * @ExternalContentMarkup(
 *   id = "plain_text",
 *   label = @Translation("Plain text"),
 *   markup_identifiers = {"txt"},
 * )
 */
final class PlainText extends PluginBase implements MarkupInterface {

  /**
   * {@inheritdoc}
   */
  public function convert(string $content): string {
    return \_filter_autop($content);
  }

}
