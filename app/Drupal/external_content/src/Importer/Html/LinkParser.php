<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\LinkNode;

final class LinkParser implements HtmlNodeParser {

  public function supports(HtmlParserRequest $request): bool {
    return $request->htmlNode instanceof \DOMElement && $request->htmlNode->nodeName === 'a';
  }

  public function parse(HtmlParserRequest $request): ContentNode {
    \assert($request->htmlNode instanceof \DOMElement);
    $link_node = new LinkNode(
      $request->htmlNode->getAttribute('href'),
      $request->htmlNode->getAttribute('target'),
      $request->htmlNode->getAttribute('rel'),
      $request->htmlNode->getAttribute('title'),
    );
    $request->importRequest->getHtmlParser()->parseChildren($request->withContentNode($link_node));

    return $link_node;
  }

}
