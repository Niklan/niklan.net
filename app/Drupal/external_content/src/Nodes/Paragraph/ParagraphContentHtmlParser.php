<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Paragraph;

use Drupal\external_content\Contract\Importer\ContentHtmlParser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final class ParagraphContentHtmlParser implements ContentHtmlParser {

  public function supports(HtmlParseRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->nodeName === 'p';
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    $paragraph = new ParagraphNode();
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($paragraph));

    return $paragraph;
  }

}
