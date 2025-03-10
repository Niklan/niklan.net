<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\Importer;
use Drupal\external_content\Contract\Importer\ImportRequest;
use Drupal\external_content\Node\RootNode;
use Symfony\Component\DomCrawler\Crawler;

final readonly class HtmlImporter implements Importer {

  /**
   * @param \Drupal\external_content\Importer\Html\HtmlImportRequest $request
   */
  public function import(ImportRequest $request): RootNode {
    $content_root_node = new RootNode();
    $this->parseHtml($content_root_node, $request);

    return $content_root_node;
  }

  private function parseHtml(RootNode $content_root_node, HtmlImportRequest $request): void {
    // @todo HtmlSource
    // @todo HtmlParserState?
    $crawler = new Crawler($context->rawHtmlContent);
    $crawler = $crawler->filter('body');
    $html_body = $crawler->getNode(0);
    \assert($html_body instanceof \DOMNode);
    $context->getHtmNodeChildrenTransformer()->parseChildren($html_body, $content_root_node, $context);
  }

}
