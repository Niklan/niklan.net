<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\ListItem;

use Drupal\external_content\Contract\Importer\ContentHtmlParser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final class ListItemContentHtmlParser implements ContentHtmlParser {

  public function supports(HtmlParseRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->nodeName === 'li';
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    $list_item_node = new ListItemNode();
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($list_item_node));

    return $list_item_node;
  }

}
