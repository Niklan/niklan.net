<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\ThematicBreakNode;

final class ThematicBreakParser implements HtmlNodeParser {

  public function supports(HtmlParserRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->nodeName === 'hr';
  }

  public function parse(HtmlParserRequest $request): ContentNode {
    return new ThematicBreakNode();
  }

}
