<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\Contract\Importer\ContentHtmlParser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\ContentNode;
use Symfony\Component\DomCrawler\Crawler;

final readonly class CalloutContentHtmlParser implements ContentHtmlParser {

  public function supports(HtmlParseRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMElement) {
      return FALSE;
    }

    return $request->currentHtmlNode->getAttribute('data-selector') === 'niklan:container-directive'
      && \in_array(
        $request->currentHtmlNode->getAttribute('data-type'),
        ['note', 'tip', 'important', 'warning', 'caution'],
        TRUE,
      );
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    $callout = new CalloutNode($request->currentHtmlNode->getAttribute('data-type'));
    $this->parseTitle($request->withNewContentNode($callout));
    $this->parseBody($request->withNewContentNode($callout));

    return $callout;
  }

  private function parseTitle(HtmlParseRequest $request): void {
    $crawler = new Crawler($request->currentHtmlNode);
    $title_node = $crawler->filter('[data-selector="inline-content"]')->getNode(0);
    if (!$title_node instanceof \DOMNode) {
      return;
    }

    $title = new CalloutTitleNode();
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewNodes($title_node, $title));
    $request->currentAstNode->addChild($title);
  }

  private function parseBody(HtmlParseRequest $request): void {
    $crawler = new Crawler($request->currentHtmlNode);
    $body_node = $crawler->filter('[data-selector="content"]')->getNode(0);
    if (!$body_node instanceof \DOMNode) {
      return;
    }

    $body = new CalloutBodyNode();
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewNodes($body_node, $body));
    $request->currentAstNode->addChild($body);
  }

}
