<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\ParagraphNode;

final class ParagraphParser implements HtmlNodeParser {

  public function supports(\DOMNode $node, HtmlImporterContext $context): bool {
    return $node instanceof \DOMElement && $node->nodeName === 'p';
  }

  public function parse(\DOMNode $node, HtmlImporterContext $context): ContentNode {
    $paragraph = new ParagraphNode();
    $context->getHtmNodeChildrenTransformer()->parseChildren($node, $paragraph, $context);

    return $paragraph;
  }

}
