<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Domain\HeadingTagType;
use Drupal\external_content\Node\ContentNode;
use Drupal\external_content\Node\HeadingNode;

final class HeadingParser implements HtmlNodeParser {

  public function supports(HtmlParserRequest $request): bool {
    if (!$request->htmlNode instanceof \DOMElement) {
      return FALSE;
    }

    $heading_elements = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    return \in_array($request->htmlNode->nodeName, $heading_elements);
  }

  public function parse(HtmlParserRequest $request): ContentNode {
    \assert($request->htmlNode instanceof \DOMElement);
    $heading = new HeadingNode(HeadingTagType::fromHtmlTag($request->htmlNode->nodeName));
    $request->importRequest->getHtmlParser()->parseChildren($request->withContentNode($heading));

    return $heading;
  }

}
