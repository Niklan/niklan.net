<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html\Parser;

use Drupal\external_content\Contract\DataStructure\HtmlNodeParser;
use Drupal\external_content\DataStructure\Nodes\ContentNode;
use Drupal\external_content\DataStructure\Nodes\ThematicBreakNode;

final class ThematicBreakParser implements HtmlNodeParser {

  public function supports(HtmlParseRequest $request): bool {
    return $request->currentHtmlNode instanceof \DOMElement && $request->currentHtmlNode->nodeName === 'hr';
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    return new ThematicBreakNode();
  }

}
