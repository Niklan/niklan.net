<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\RemoteVideo;

use Drupal\external_content\Contract\Parser\Html\ChildParser;
use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Nodes\Node;

final readonly class HtmlParser implements Parser {

  public function supports(\DOMNode $dom_node): bool {
    return $dom_node instanceof \DOMElement
      && $dom_node->getAttribute('data-selector') === 'niklan:leaf-directive'
      && $dom_node->getAttribute('data-type') === 'youtube'
      && $dom_node->hasAttribute('data-vid');
  }

  public function parseElement(\DOMNode $dom_node, ChildParser $child_parser): Node {
    \assert($dom_node instanceof \DOMElement);
    $video_id = $dom_node->getAttribute('data-vid');
    return new RemoteVideo("https://youtu.be/$video_id");
  }

}
