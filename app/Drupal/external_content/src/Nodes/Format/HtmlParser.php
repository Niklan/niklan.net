<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Format;

use Drupal\external_content\Contract\Importer\Html\Parser;
use Drupal\external_content\Domain\TextFormatType;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\Content\Content;

final class HtmlParser implements Parser {

  public function supports(HtmlParseRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMElement) {
      return FALSE;
    }

    $format_elements = [
      'strong', 'b', 'em', 'u', 's', 'i', 'mark', 'code', 'sub', 'sup',
    ];

    return \in_array($request->currentHtmlNode->nodeName, $format_elements);
  }

  public function parse(HtmlParseRequest $request): Content {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    $format_node = new Format(TextFormatType::fromHtmlTag($request->currentHtmlNode->nodeName));
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($format_node));

    return $format_node;
  }

}
