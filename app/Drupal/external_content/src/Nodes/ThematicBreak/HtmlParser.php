<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\ThematicBreak;

use Drupal\external_content\Contract\Importer\Html\Parser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final class HtmlParser implements Parser {

  public function supports(HtmlParseRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->nodeName === 'hr';
  }

  public function parse(HtmlParseRequest $request): Content {
    return new ThematicBreak();
  }

}
