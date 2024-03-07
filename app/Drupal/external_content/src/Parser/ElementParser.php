<?php declare(strict_types = 1);

namespace Drupal\external_content\Parser;

use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Node\Html\Element;

/**
 * Provides a generic HTML element parser.
 */
final class ElementParser implements HtmlParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parseNode(\DOMNode $node): HtmlParserResult {
    $element = new Element($node->nodeName);

    if ($node->hasAttributes()) {
      foreach ($node->attributes->getIterator() as $attribute) {
        \assert($attribute instanceof \DOMAttr);
        $element
          ->getAttributes()
          ->setAttribute($attribute->name, $attribute->value);
      }
    }

    return HtmlParserResult::replace($element);
  }

}
