<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Image;

use Drupal\external_content\Contract\Importer\Html\Parser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final class HtmlParser implements Parser {

  public function supports(HtmlParseRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->nodeName === 'img';
  }

  public function parse(HtmlParseRequest $request): Content {
    \assert($request->currentHtmlNode instanceof \DOMElement);

    return new Image($request->currentHtmlNode->getAttribute('src'), $request->currentHtmlNode->getAttribute('alt'));
  }

}
