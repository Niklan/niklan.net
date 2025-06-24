<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Video;

use Drupal\external_content\Contract\Importer\ContentHtmlParser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\ContentNode;
use Symfony\Component\DomCrawler\Crawler;

final readonly class VideoContentHtmlParser implements ContentHtmlParser {

  public function supports(HtmlParseRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMElement) {
      return FALSE;
    }

    if ($request->currentHtmlNode->getAttribute('data-selector') !== 'niklan:leaf-directive') {
      return FALSE;
    }

    if ($request->currentHtmlNode->getAttribute('data-type') !== 'video') {
      return FALSE;
    }

    return $request->currentHtmlNode->hasAttribute('data-argument');
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    return new VideoNode(
      src: $request->currentHtmlNode->getAttribute('data-argument'),
      title: $this->prepareTitle($request),
    );
  }

  private function prepareTitle(HtmlParseRequest $request): string {
    $title = 'Local video';
    $crawler = new Crawler($request->currentHtmlNode);
    $crawler = $crawler->filter('[data-selector="inline-content"]');
    return $crawler->count() ? $crawler->text() : $title;
  }

}
