<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html\Parser;

use Drupal\external_content\Contract\DataStructure\HtmlNodeParser;
use Drupal\external_content\DataStructure\Nodes\ContentNode;
use Drupal\external_content\DataStructure\Nodes\LinkNode;

final class LinkParser implements HtmlNodeParser {

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
