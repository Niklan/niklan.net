<?php

declare(strict_types=1);

namespace Drupal\external_content\Parser;

use Drupal\external_content\Contract\Parser\ChildHtmlParserInterface;
use Drupal\external_content\Contract\Parser\HtmlParserInterface;
use Drupal\external_content\Data\HtmlParserResult;
use Drupal\external_content\Node\Element;

/**
 * Provides a generic HTML element parser.
 */
final class ElementParser implements HtmlParserInterface {

  #[\Override]
  public function parseNode(\DOMNode $node, ChildHtmlParserInterface $child_parser): HtmlParserResult {
    $element = new Element($node->nodeName);

    if ($node->hasAttributes()) {
      // @todo Remove when resolved: https://github.com/phpstan/phpstan/issues/12495
      \assert($node->attributes instanceof \DOMNamedNodeMap);
      foreach ($node->attributes->getIterator() as $attribute) {
        \assert($attribute instanceof \DOMAttr);
        $element->getAttributes()->setAttribute($attribute->name, $attribute->value);
      }
    }

    $element->addChildren($child_parser->parse($node->childNodes));

    return HtmlParserResult::replace($element);
  }

}
