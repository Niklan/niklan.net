<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Markup;

use Drupal\Core\Plugin\PluginBase;
use Drupal\external_content\Contract\MarkupPluginInterface;

/**
 * Represents a raw HTML markup.
 *
 * @ExternalContentMarkup(
 *   id = "html",
 *   label = @Translation("Plain text"),
 *   markup_identifiers = {"html", "htm"},
 * )
 */
final class Html extends PluginBase implements MarkupPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function convert(string $content): string {
    return $content;
  }

}
