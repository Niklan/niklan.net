<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Heading;

use Drupal\external_content\Contract\Parser\Html\ChildParser;
use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Domain\HeadingTagType;
use Drupal\external_content\Nodes\Node;

final class HtmlParser implements Parser {

  public function supports(\DOMNode $dom_node): bool {
    if (!$dom_node instanceof \DOMElement) {
      return FALSE;
    }
    return \in_array($dom_node->nodeName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']);
  }

  public function parseElement(\DOMNode $dom_node, ChildParser $child_parser): Node {
    \assert($dom_node instanceof \DOMElement);
    $heading = new Heading(HeadingTagType::fromHtmlTag($dom_node->nodeName));
    $child_parser->parseChildren($dom_node, $heading);
    return $heading;
  }

}
