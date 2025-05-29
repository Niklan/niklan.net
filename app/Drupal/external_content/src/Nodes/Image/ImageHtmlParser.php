<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Image;

use Drupal\external_content\Contract\DataStructure\HtmlNodeParser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final class ImageHtmlParser implements HtmlNodeParser {

  public function supports(HtmlParseRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->nodeName === 'img';
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    \assert($request->currentHtmlNode instanceof \DOMElement);

    return new ImageNode($request->currentHtmlNode->getAttribute('src'), $request->currentHtmlNode->getAttribute('alt'));
  }

}
