<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Plugin\ExternalContent\HtmlParser;

use Drupal\external_content\Contract\ElementInterface;
use Drupal\external_content\Contract\HtmlParserPluginInterface;
use Drupal\external_content\Contract\HtmlParserStateInterface;
use Drupal\external_content_test\Dto\FooBarElement;

/**
 * Provides a foo bar HTML parser.
 *
 * @ExternalContentHtmlParser(
 *   id = "foo_bar",
 *   label = @Translation("Foo Bar"),
 * )
 */
final class FooBar implements HtmlParserPluginInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(\DOMNode $node): bool {
    return $node->nodeName === 'foo-bar';
  }

  /**
   * {@inheritdoc}
   */
  public function parse(\DOMNode $node, HtmlParserStateInterface $html_parser_state): ElementInterface {
    $element = new FooBarElement();

    if ($node->hasChildNodes()) {
      foreach ($node->childNodes as $child_node) {
        $child_element = $html_parser_state->getParser()
          ->parseElement($child_node, $html_parser_state);
        $element->addChild($child_element);
      }
    }

    return $element;
  }

}
