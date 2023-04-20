<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Plugin\ExternalContent\Markup;

use Drupal\external_content\Contract\MarkupPluginInterface;

/**
 * Provides fake markup which replaces "bar" by "baz" HTML.
 *
 * @ExternalContentMarkup(
 *   id = "external_content_test_bar_baz",
 *   label = @Translation("bar baz"),
 *   markup_identifiers = {"bar"},
 * )
 */
final class BarBaz implements MarkupPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function convert(string $content): string {
    return \str_replace('bar', 'baz', $content);
  }

}
