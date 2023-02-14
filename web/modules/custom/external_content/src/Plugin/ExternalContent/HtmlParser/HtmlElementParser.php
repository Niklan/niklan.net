<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\HtmlParser;

use Drupal\external_content\Dto\ElementInterface;
use Drupal\external_content\Dto\HtmlElement;
use Drupal\external_content\Dto\HtmlParserStateInterface;

/**
 * Provides a common HTML element parser plugin.
 *
 * This plugin will be used for most HTML elements if no other parsers is
 * suitable for it.
 *
 * @ExternalContentHtmlParser(
 *   id = "html_element",
 *   label = @Translation("HTML Element"),
 *   weight = 900,
 * )
 */
final class HtmlElementParser implements HtmlParserInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(\DOMNode $node): bool {
    return $node instanceof \DOMElement;
  }

  /**
   * {@inheritdoc}
   */
  public function parse(\DOMNode $node, HtmlParserStateInterface $html_parser_state): ElementInterface {
    $element = new HtmlElement($node->nodeName);
    if ($node->hasAttributes()) {
      /** @var \DOMAttr $attribute */
      foreach ($node->attributes->getIterator() as $attribute) {
        $element->setAttribute($attribute->name, $attribute->value);
      }
    }
    foreach ($node->childNodes as $child) {
      $child = $html_parser_state->getParser()
        ->parseElement($child, $html_parser_state);
      $element->addChild($child);
    }

    return $element;
  }

}
