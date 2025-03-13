<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\ListItemNode;

final class ListItemParser implements HtmlNodeParser {

  public function supports(HtmlParserRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->nodeName === 'li';
  }

  public function parse(HtmlParserRequest $request): ContentNode {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    $list_item_node = new ListItemNode();
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($list_item_node));

    return $list_item_node;
  }

}
