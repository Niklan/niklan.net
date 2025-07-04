<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Parser\Html\HtmlParseRequest;
use Drupal\niklan\ExternalContent\Nodes\CalloutBody\CalloutBody;
use Drupal\niklan\ExternalContent\Nodes\CalloutTitle\CalloutTitle;
use Symfony\Component\DomCrawler\Crawler;

final readonly class HtmlParser implements Parser {

  public function supports(HtmlParseRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMElement) {
      return FALSE;
    }

    return $request->currentHtmlNode->getAttribute('data-selector') === 'niklan:container-directive'
      && \in_array(
        needle: $request->currentHtmlNode->getAttribute('data-type'),
        haystack: ['note', 'tip', 'important', 'warning', 'caution'],
        strict: TRUE,
      );
  }

  public function parse(HtmlParseRequest $request): Node {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    $callout = new Callout($request->currentHtmlNode->getAttribute('data-type'));
    $this->parseTitle($request->withNewContentNode($callout));
    $this->parseBody($request->withNewContentNode($callout));

    return $callout;
  }

  private function parseTitle(HtmlParseRequest $request): void {
    $crawler = new Crawler($request->currentHtmlNode);
    // Since callouts can be nested within each other, there is a possibility
    // that a parent callout may not have a title while a nested one does.
    // Therefore, it is crucial that the title is searched only among direct
    // child elements and not throughout the entire child tree.
    $title_node = $crawler->filterXPath('.//*[1]/div[@data-selector="inline-content"]')->getNode(0);
    if (!$title_node instanceof \DOMNode) {
      return;
    }

    $title = new CalloutTitle();
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewNodes($title_node, $title));
    $request->currentAstNode->addChild($title);
  }

  private function parseBody(HtmlParseRequest $request): void {
    $crawler = new Crawler($request->currentHtmlNode);
    $body_node = $crawler->filter('[data-selector="content"]')->getNode(0);
    if (!$body_node instanceof \DOMNode) {
      return;
    }

    $body = new CalloutBody();
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewNodes($body_node, $body));
    $request->currentAstNode->addChild($body);
  }

}
