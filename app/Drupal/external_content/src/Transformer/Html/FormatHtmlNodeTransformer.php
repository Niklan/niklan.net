<?php

declare(strict_types=1);

namespace Drupal\external_content\Transformer\Html;

use Drupal\external_content\Contract\Transformer\HtmlNodeTransformer;
use Drupal\external_content\Domain\TextFormatType;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\FormatNode;

final class FormatHtmlNodeTransformer implements HtmlNodeTransformer {

  public function supports(\DOMNode $node, HtmlTransformerContext $context): bool {
    $format_elements = [
      'strong', 'b', 'em', 'u', 's', 'i', 'mark', 'code', 'sub', 'sup',
    ];

    return $node instanceof \DOMElement && \in_array($node->nodeName, $format_elements);
  }

  public function transform(\DOMNode $node, HtmlTransformerContext $context): ContentNode {
    \assert($node instanceof \DOMElement);
    $format_node = new FormatNode(TextFormatType::fromHtmlTag($node->nodeName));
    $context->getHtmNodeChildrenTransformer()->transformChildren($node, $format_node, $context);

    return $format_node;
  }

}
