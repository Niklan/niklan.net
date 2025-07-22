<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\LocalVideo;

use Drupal\external_content\Contract\Parser\Html\ChildParser;
use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Utils\HtmlDomHelper;
use Symfony\Component\DomCrawler\Crawler;

final readonly class HtmlParser implements Parser {

  public function supports(\DOMNode $dom_node): bool {
    return $dom_node instanceof \DOMElement
      && $dom_node->getAttribute('data-selector') === 'niklan:leaf-directive'
      && $dom_node->getAttribute('data-type') === 'video'
      && $dom_node->hasAttribute('data-argument');
  }

  public function parseElement(\DOMNode $dom_node, ChildParser $child_parser): Node {
    \assert($dom_node instanceof \DOMElement);
    return new LocalVideo(
      src: $dom_node->getAttribute('data-argument'),
      title: $this->prepareTitle($dom_node),
      attributes: \array_intersect_key(
        HtmlDomHelper::parseAttributes($dom_node),
        \array_flip(['autoplay', 'loop', 'controls', 'muted']),
      ),
    );
  }

  private function prepareTitle(\DOMNode $dom_node): string {
    $title = 'Local video';
    $crawler = new Crawler($dom_node);
    $crawler = $crawler->filter('[data-selector="inline-content"]');
    return $crawler->count() ? $crawler->text() : $title;
  }

}
