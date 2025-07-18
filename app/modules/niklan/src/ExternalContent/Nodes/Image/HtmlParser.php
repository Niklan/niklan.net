<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Image;

use Drupal\external_content\Contract\Parser\Html\ChildParser;
use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Nodes\Node;

final readonly class HtmlParser implements Parser {

  public function supports(\DOMNode $dom_node): bool {
    return $dom_node instanceof \DOMElement && $dom_node->nodeName === 'img';
  }

  public function parseElement(\DOMNode $dom_node, ChildParser $child_parser): Node {
    \assert($dom_node instanceof \DOMElement);
    return new Image($dom_node->getAttribute('src'), $dom_node->getAttribute('alt'));
  }

}
