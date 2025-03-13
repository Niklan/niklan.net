<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\CodeNode;
use Drupal\external_content\Node\ContentNode;

final class CodeParser implements HtmlNodeParser {

  public function supports(HtmlParserRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMElement) {
      return FALSE;
    }

    return $request->currentHtmlNode->nodeName === 'pre' && $request->currentHtmlNode->firstChild?->nodeName === 'code';
  }

  public function parse(HtmlParserRequest $request): ContentNode {
    \assert($request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->firstChild instanceof \DOMElement);

    return new CodeNode($request->currentHtmlNode->firstChild->textContent);
  }

}
