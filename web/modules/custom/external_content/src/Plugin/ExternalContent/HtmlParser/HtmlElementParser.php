<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\HtmlParser;

use Drupal\external_content\Contract\ElementInterface;
use Drupal\external_content\Contract\HtmlParserPluginInterface;
use Drupal\external_content\Contract\HtmlParserStateInterface;
use Drupal\external_content\Data\HtmlElement;

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
final class HtmlElementParser implements HtmlParserPluginInterface {

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
      foreach ($node->attributes->getIterator() as $attribute) {
        \assert($attribute instanceof \DOMAttr);
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
