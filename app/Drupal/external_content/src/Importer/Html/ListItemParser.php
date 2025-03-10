<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\ListItemNode;

final class ListItemParser implements HtmlNodeParser {

  public function supports(\DOMNode $node, HtmlImporterContext $context): bool {
    return $node instanceof \DOMElement && $node->nodeName === 'li';
  }

  public function parse(\DOMNode $node, HtmlImporterContext $context): ContentNode {
    \assert($node instanceof \DOMElement);
    $list_item_node = new ListItemNode();
    $context->getHtmNodeChildrenTransformer()->parseChildren($node, $list_item_node, $context);

    return $list_item_node;
  }

}
