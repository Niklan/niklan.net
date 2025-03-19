<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html\Parser;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\ParagraphNode;

final class ParagraphParser implements HtmlNodeParser {

  public function supports(HtmlParseRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->nodeName === 'p';
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    $paragraph = new ParagraphNode();
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($paragraph));

    return $paragraph;
  }

}
