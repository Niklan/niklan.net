<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Figure;

use Drupal\external_content\Contract\Parser\Html\ChildParser;
use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Nodes\Node;
use Drupal\niklan\ExternalContent\Utils\ContainerDirectiveHelper;

final readonly class HtmlParser implements Parser {

  public function supports(\DOMNode $dom_node): bool {
    return $dom_node instanceof \DOMElement
      && $dom_node->getAttribute('data-selector') === 'niklan:container-directive'
      && $dom_node->getAttribute('data-type') === 'figure';
  }

  public function parseElement(\DOMNode $dom_node, ChildParser $child_parser): Node {
    $node = new Figure();
    $content = ContainerDirectiveHelper::findDomContent($dom_node);
    if (!$content instanceof \DOMNode) {
      return $node;
    }

    $child_parser->parseChildren($content, $node);
    return $node;
  }

}
