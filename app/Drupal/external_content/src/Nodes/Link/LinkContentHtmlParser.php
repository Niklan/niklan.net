<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Link;

use Drupal\external_content\Contract\Importer\ContentHtmlParser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final class LinkContentHtmlParser implements ContentHtmlParser {

  public function supports(HtmlParseRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->nodeName === 'a';
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    $link_node = new LinkNode(
      $request->currentHtmlNode->getAttribute('href'),
      $request->currentHtmlNode->getAttribute('target'),
      $request->currentHtmlNode->getAttribute('rel'),
      $request->currentHtmlNode->getAttribute('title'),
    );
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($link_node));

    return $link_node;
  }

}
