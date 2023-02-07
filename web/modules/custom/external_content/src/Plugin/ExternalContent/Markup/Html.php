<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Markup;

use Drupal\Core\Plugin\PluginBase;

/**
 * Represents a raw HTML markup.
 *
 * @ExternalContentMarkup(
 *   id = "html",
 *   label = @Translation("Plain text"),
 *   markup_identifiers = {"html", "htm"},
 * )
 */
final class Html extends PluginBase implements MarkupInterface {

  /**
   * {@inheritdoc}
   */
  public function convert(string $content): string {
    return $content;
  }

}
