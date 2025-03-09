<?php

declare(strict_types=1);

namespace Drupal\external_content\Transformer\Html;

use Drupal\external_content\Contract\Transformer\HtmlNodeTransformer;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\ParagraphNode;

final class ParagraphHtmlNodeTransformer implements HtmlNodeTransformer {

  public function supports(\DOMNode $node, HtmlTransformerContext $context): bool {
    return $node instanceof \DOMElement && $node->nodeName === 'p';
  }

  public function transform(\DOMNode $node, HtmlTransformerContext $context): ContentNode {
    $paragraph = new ParagraphNode();
    $context->getHtmNodeChildrenTransformer()->transformChildren($node, $paragraph, $context);

    return $paragraph;
  }

}
