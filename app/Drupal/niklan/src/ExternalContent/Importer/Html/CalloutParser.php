<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Importer\Html;

use Drupal\external_content\Contract\DataStructure\HtmlNodeParser;
use Drupal\external_content\DataStructure\Nodes\ContentNode;
use Drupal\external_content\DataStructure\Nodes\RootNode;
use Drupal\external_content\Importer\Html\Parser\HtmlParseRequest;
use Drupal\niklan\ExternalContent\DataStructure\Nodes\CalloutNode;
use Symfony\Component\DomCrawler\Crawler;

final readonly class CalloutParser implements HtmlNodeParser {

  public function supports(HtmlParseRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMElement) {
      return FALSE;
    }

    if (!$request->currentHtmlNode->hasAttribute('data-selector') || $request->currentHtmlNode->getAttribute('data-selector') !== 'niklan:container-directive') {
      return FALSE;
    }

    $allowed_types = ['note', 'tip', 'important', 'warning', 'caution'];

    return \in_array($request->currentHtmlNode->getAttribute('data-type'), $allowed_types);
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    $callout = new CalloutNode($request->currentHtmlNode->getAttribute('data-type'));
    $this->parseTitle($request->withNewContentNode($callout));
    $this->parseContent($request->withNewContentNode($callout));

    return $callout;
  }

  private function parseTitle(HtmlParseRequest $request): void {
    $crawler = new Crawler($request->currentHtmlNode);
    $crawler = $crawler->filter('[data-selector="inline-content"]');

    if (!$crawler->count() || !$crawler->getNode(0) instanceof \DOMNode) {
      return;
    }

    $heading = new RootNode();
    $sub_request = $request->withNewNodes($crawler->getNode(0), $heading);
    $sub_request->importRequest->getHtmlParser()->parseChildren($sub_request);

    $callout = $request->currentAstNode;
    \assert($callout instanceof CalloutNode);
    foreach ($heading->getChildren() as $child) {
      $callout->addTitleChild($child);
    }
  }

  private function parseContent(HtmlParseRequest $request): void {
    $crawler = new Crawler($request->currentHtmlNode);
    $crawler = $crawler->filter('[data-selector="content"]');

    if (!$crawler->count() || !$crawler->getNode(0) instanceof \DOMNode) {
      return;
    }

    $request->importRequest->getHtmlParser()->parseChildren($request->withNewHtmlNode($crawler->getNode(0)));
  }

}
