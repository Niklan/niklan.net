<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Domain\TextFormatType;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\FormatNode;

final class FormatParser implements HtmlNodeParser {

  public function supports(\DOMNode $node, HtmlImporterContext $context): bool {
    $format_elements = [
      'strong', 'b', 'em', 'u', 's', 'i', 'mark', 'code', 'sub', 'sup',
    ];

    return $node instanceof \DOMElement && \in_array($node->nodeName, $format_elements);
  }

  public function parse(\DOMNode $node, HtmlImporterContext $context): ContentNode {
    \assert($node instanceof \DOMElement);
    $format_node = new FormatNode(TextFormatType::fromHtmlTag($node->nodeName));
    $context->getHtmNodeChildrenTransformer()->parseChildren($node, $format_node, $context);

    return $format_node;
  }

}
