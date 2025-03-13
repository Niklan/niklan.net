<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\ParagraphNode;

final class ParagraphParser implements HtmlNodeParser {

  public function supports(HtmlParserRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->nodeName === 'p';
  }

  public function parse(HtmlParserRequest $request): ContentNode {
    $paragraph = new ParagraphNode();
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($paragraph));

    return $paragraph;
  }

}
