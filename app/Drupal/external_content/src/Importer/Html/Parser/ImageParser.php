<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html\Parser;

use Drupal\external_content\Contract\DataStructure\HtmlNodeParser;
use Drupal\external_content\DataStructure\Nodes\ContentNode;
use Drupal\external_content\DataStructure\Nodes\ImageNode;

final class ImageParser implements HtmlNodeParser {

  public function supports(HtmlParseRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->nodeName === 'img';
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    \assert($request->currentHtmlNode instanceof \DOMElement);

    return new ImageNode($request->currentHtmlNode->getAttribute('src'), $request->currentHtmlNode->getAttribute('alt'));
  }

}
