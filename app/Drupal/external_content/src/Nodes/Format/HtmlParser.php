<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Format;

use Drupal\external_content\Contract\Parser\Html\ChildParser;
use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Domain\TextFormatType;
use Drupal\external_content\Nodes\Node;

final class HtmlParser implements Parser {

  public function supports(\DOMNode $dom_node): bool {
    if (!$dom_node instanceof \DOMElement) {
      return FALSE;
    }

    return \in_array($dom_node->nodeName, [
      'strong', 'b', 'em', 'u', 's', 'i', 'mark', 'code', 'sub', 'sup',
    ]);
  }

  public function parseElement(\DOMNode $dom_node, ChildParser $child_parser): Node {
    \assert($dom_node instanceof \DOMElement);
    $format_node = new Format(TextFormatType::fromHtmlTag($dom_node->nodeName));
    $child_parser->parseChildren($dom_node, $format_node);
    return $format_node;
  }

}
