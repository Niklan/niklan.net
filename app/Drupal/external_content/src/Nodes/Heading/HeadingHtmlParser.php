<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Heading;

use Drupal\external_content\Contract\DataStructure\HtmlNodeParser;
use Drupal\external_content\Domain\HeadingTagType;
use Drupal\external_content\Importer\Html\HtmlParseRequest;
use Drupal\external_content\Nodes\ContentNode;

final class HeadingHtmlParser implements HtmlNodeParser {

  public function supports(HtmlParseRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMElement) {
      return FALSE;
    }

    $heading_elements = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    return \in_array($request->currentHtmlNode->nodeName, $heading_elements);
  }

  public function parse(HtmlParseRequest $request): ContentNode {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    $heading = new HeadingNode(HeadingTagType::fromHtmlTag($request->currentHtmlNode->nodeName));
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($heading));

    return $heading;
  }

}
