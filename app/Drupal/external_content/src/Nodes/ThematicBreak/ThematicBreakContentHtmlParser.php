<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\ThematicBreak;

use Drupal\external_content\Contract\Importer\ContentHtmlParser;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final class ThematicBreakContentHtmlParser implements ContentHtmlParser {

  public function supports(HtmlParseRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->nodeName === 'hr';
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    return new ThematicBreakNode();
  }

}
