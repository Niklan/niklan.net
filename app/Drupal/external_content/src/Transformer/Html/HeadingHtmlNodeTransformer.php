<?php

declare(strict_types=1);

namespace Drupal\external_content\Transformer\Html;

use Drupal\external_content\Contract\Transformer\HtmlNodeTransformer;
use Drupal\external_content\Domain\HeadingTagType;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\HeadingNode;

final class HeadingHtmlNodeTransformer implements HtmlNodeTransformer {

  public function supports(\DOMNode $node, HtmlTransformerContext $context): bool {
    $heading_elements = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    return $node instanceof \DOMElement && \in_array($node->nodeName, $heading_elements);
  }

  public function transform(\DOMNode $node, HtmlTransformerContext $context): ContentNode {
    \assert($node instanceof \DOMElement);
    $heading = new HeadingNode(HeadingTagType::fromHtmlTag($node->nodeName));
    $context->getHtmNodeChildrenTransformer()->transformChildren($node, $heading, $context);

    return $heading;
  }

}
