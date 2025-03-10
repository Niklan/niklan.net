<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\CodeNode;
use Drupal\external_content\Node\ContentNode;

final class CodeParser implements HtmlNodeParser {

  public function supports(\DOMNode $node, HtmlImporterContext $context): bool {
    return $node instanceof \DOMElement && $node->nodeName === 'pre' && $node->firstChild?->nodeName === 'code';
  }

  public function parse(\DOMNode $node, HtmlImporterContext $context): ContentNode {
    \assert($node instanceof \DOMElement && $node->firstChild instanceof \DOMElement);

    return new CodeNode($node->firstChild->textContent);
  }

}
