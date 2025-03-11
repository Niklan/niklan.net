<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\CodeNode;
use Drupal\external_content\Node\ContentNode;

final class CodeParser implements HtmlNodeParser {

  public function supports(HtmlParserRequest $request): bool {
    if (!$request->htmlNode instanceof \DOMElement) {
      return FALSE;
    }

    return $request->htmlNode->nodeName === 'pre' && $request->htmlNode->firstChild?->nodeName === 'code';
  }

  public function parse(HtmlParserRequest $request): ContentNode {
    \assert($request->htmlNode instanceof \DOMElement && $request->htmlNode->firstChild instanceof \DOMElement);

    return new CodeNode($request->htmlNode->firstChild->textContent);
  }

}
