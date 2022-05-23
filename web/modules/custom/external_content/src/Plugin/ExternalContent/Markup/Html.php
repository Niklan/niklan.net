<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Markup;

/**
 * Represents a raw HTML markup.
 *
 * @ExternalContentMarkup(
 *   id = "html",
 *   label = @Translation("Plain text"),
 *   markup_identifiers = {"html", "htm"},
 * )
 */
final class Html implements MarkupInterface {

  /**
   * {@inheritdoc}
   */
  public function convert(string $content): string {
    return $content;
  }

}
