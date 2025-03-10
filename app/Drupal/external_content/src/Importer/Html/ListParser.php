<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Domain\ListType;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\ListNode;

final class ListParser implements HtmlNodeParser {

  public function supports(\DOMNode $node, HtmlImporterContext $context): bool {
    $list_elements = ['ul', 'ol'];

    return $node instanceof \DOMElement && \in_array($node->nodeName, $list_elements);
  }

  public function parse(\DOMNode $node, HtmlImporterContext $context): ContentNode {
    \assert($node instanceof \DOMElement);
    $list_node = new ListNode(ListType::fromHtmlTag($node->nodeName));
    $context->getHtmNodeChildrenTransformer()->parseChildren($node, $list_node, $context);

    return $list_node;
  }

}
