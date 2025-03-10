<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\LinkNode;

final class LinkParser implements HtmlNodeParser {

  public function supports(\DOMNode $node, HtmlImporterContext $context): bool {
    return $node instanceof \DOMElement && $node->nodeName === 'a';
  }

  public function parse(\DOMNode $node, HtmlImporterContext $context): ContentNode {
    \assert($node instanceof \DOMElement);
    $link_node = new LinkNode(
      $node->getAttribute('href'),
      $node->getAttribute('target'),
      $node->getAttribute('rel'),
      $node->getAttribute('title'),
    );
    $context->getHtmNodeChildrenTransformer()->parseChildren($node, $link_node, $context);

    return $link_node;
  }

}
