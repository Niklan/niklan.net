<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Domain\ListType;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\ListNode;

final class ListParser implements HtmlNodeParser {

  public function supports(HtmlParserRequest $request): bool {
    $list_elements = ['ul', 'ol'];

    return $request->htmlNode instanceof \DOMElement && \in_array($request->htmlNode->nodeName, $list_elements);
  }

  public function parse(HtmlParserRequest $request): ContentNode {
    \assert($request->htmlNode instanceof \DOMElement);
    $list_node = new ListNode(ListType::fromHtmlTag($request->htmlNode->nodeName));
    $request->importRequest->getHtmlParser()->parseChildren($request->withContentNode($list_node));

    return $list_node;
  }

}
