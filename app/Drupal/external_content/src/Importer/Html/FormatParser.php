<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Domain\TextFormatType;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\FormatNode;

final class FormatParser implements HtmlNodeParser {

  public function supports(HtmlParserRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMElement) {
      return FALSE;
    }

    $format_elements = [
      'strong', 'b', 'em', 'u', 's', 'i', 'mark', 'code', 'sub', 'sup',
    ];

    return \in_array($request->currentHtmlNode->nodeName, $format_elements);
  }

  public function parse(HtmlParserRequest $request): ContentNode {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    $format_node = new FormatNode(TextFormatType::fromHtmlTag($request->currentHtmlNode->nodeName));
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($format_node));

    return $format_node;
  }

}
