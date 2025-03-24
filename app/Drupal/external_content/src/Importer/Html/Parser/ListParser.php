<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html\Parser;

use Drupal\external_content\Contract\DataStructure\HtmlNodeParser;
use Drupal\external_content\Domain\ListType;
use Drupal\external_content\DataStructure\Nodes\ContentNode;
use Drupal\external_content\DataStructure\Nodes\ListNode;

final class ListParser implements HtmlNodeParser {

  public function supports(HtmlParseRequest $request): bool {
    $list_elements = ['ul', 'ol'];

    return $request->currentHtmlNode instanceof \DOMElement && \in_array($request->currentHtmlNode->nodeName, $list_elements);
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    $list_node = new ListNode(ListType::fromHtmlTag($request->currentHtmlNode->nodeName));
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($list_node));

    return $list_node;
  }

}
