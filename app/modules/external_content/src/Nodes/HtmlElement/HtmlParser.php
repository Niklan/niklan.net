<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\HtmlElement;

use Drupal\external_content\Contract\Parser\Html\ChildParser;
use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Utils\HtmlDomHelper;

final class HtmlParser implements Parser {

  public function supports(\DOMNode $dom_node): bool {
    return TRUE;
  }

  public function parseElement(\DOMNode $dom_node, ChildParser $child_parser): Node {
    $element = new HtmlElement($dom_node->nodeName, HtmlDomHelper::parseAttributes($dom_node));
    $child_parser->parseChildren($dom_node, $element);
    return $element;
  }

}
