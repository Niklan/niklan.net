<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\Contract\Parser\Html\ChildParser;
use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Nodes\Node;
use Drupal\niklan\ExternalContent\Nodes\CalloutBody\CalloutBody;
use Drupal\niklan\ExternalContent\Nodes\CalloutTitle\CalloutTitle;
use Symfony\Component\DomCrawler\Crawler;

final readonly class HtmlParser implements Parser {

  public function supports(\DOMNode $dom_node): bool {
    if (!$dom_node instanceof \DOMElement) {
      return FALSE;
    }

    return $dom_node->getAttribute('data-selector') === 'niklan:container-directive'
      && \in_array(
        needle: $dom_node->getAttribute('data-type'),
        haystack: ['note', 'tip', 'important', 'warning', 'caution'],
        strict: TRUE,
      );
  }

  public function parseElement(\DOMNode $dom_node, ChildParser $child_parser): Node {
    \assert($dom_node instanceof \DOMElement);
    $callout = new Callout($dom_node->getAttribute('data-type'));
    $this->parseTitle($dom_node, $callout, $child_parser);
    $this->parseBody($dom_node, $callout, $child_parser);
    return $callout;
  }

  private function parseTitle(\DOMNode $dom_node, Node $callout, ChildParser $child_parser): void {
    $crawler = new Crawler($dom_node);
    // Since callouts can be nested within each other, there is a possibility
    // that a parent callout may not have a title while a nested one does.
    // Therefore, it is crucial that the title is searched only among direct
    // child elements and not throughout the entire child tree.
    $title_node = $crawler->filterXPath('.//*[1]/div[@data-selector="inline-content"]')->getNode(0);
    if (!$title_node instanceof \DOMNode) {
      return;
    }

    $title = new CalloutTitle();
    $child_parser->parseChildren($title_node, $title);
    $callout->addChild($title);
  }

  private function parseBody(\DOMNode $dom_node, Node $callout, ChildParser $child_parser): void {
    $crawler = new Crawler($dom_node);
    $body_node = $crawler->filter('[data-selector="content"]')->getNode(0);
    if (!$body_node instanceof \DOMNode) {
      return;
    }

    $body = new CalloutBody();
    $child_parser->parseChildren($body_node, $body);
    $callout->addChild($body);
  }

}
