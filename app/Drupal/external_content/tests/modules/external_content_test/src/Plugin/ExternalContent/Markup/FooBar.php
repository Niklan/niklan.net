<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Plugin\ExternalContent\Markup;

use Drupal\external_content\Plugin\ExternalContent\Markup\MarkupInterface;

/**
 * Provides fake markup which replaces "foo" by "bar" HTML.
 *
 * @ExternalContentMarkup(
 *   id = "external_content_test_foo_bar",
 *   label = @Translation("foo bar"),
 *   markup_identifiers = {"foo"},
 * )
 */
final class FooBar implements MarkupInterface {

  /**
   * {@inheritdoc}
   */
  public function convert(string $content): string {
    return \str_replace('foo', 'bar', $content);
  }

}
