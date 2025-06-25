<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Link;

use Drupal\external_content\Contract\Importer\Html\Parser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final class HtmlParser implements Parser {

  public function supports(HtmlParseRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->nodeName === 'a';
  }

  public function parse(HtmlParseRequest $request): Content {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    $link_node = new Link(
      url: $request->currentHtmlNode->getAttribute('href'),
      target: $request->currentHtmlNode->getAttribute('target'),
      rel: $request->currentHtmlNode->getAttribute('rel'),
      title: $request->currentHtmlNode->getAttribute('title'),
    );
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($link_node));
    return $link_node;
  }

}
