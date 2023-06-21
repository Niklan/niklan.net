<?php declare(strict_types = 1);

namespace Drupal\external_content\Parser;

use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Node\HtmlElement;

/**
 * Provides a generic HTML element parser.
 */
final class HtmlElementParser implements HtmlParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(\DOMNode $node): HtmlParserResult {
    $element = new HtmlElement($node->nodeName);

    if ($node->hasAttributes()) {
      foreach ($node->attributes->getIterator() as $attribute) {
        \assert($attribute instanceof \DOMAttr);
        $element->setAttribute($attribute->name, $attribute->value);
      }
    }

    return HtmlParserResult::replaceWith($element);
  }

}
