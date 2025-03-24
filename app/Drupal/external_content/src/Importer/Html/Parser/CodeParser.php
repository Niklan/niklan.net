<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html\Parser;

use Drupal\external_content\Contract\DataStructure\HtmlNodeParser;
use Drupal\external_content\DataStructure\Nodes\CodeNode;
use Drupal\external_content\DataStructure\Nodes\ContentNode;

final class CodeParser implements HtmlNodeParser {

  public function supports(HtmlParseRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMElement) {
      return FALSE;
    }

    return $request->currentHtmlNode->nodeName === 'pre' && $request->currentHtmlNode->firstChild?->nodeName === 'code';
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    \assert($request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->firstChild instanceof \DOMElement);

    return new CodeNode($request->currentHtmlNode->firstChild->textContent);
  }

}
