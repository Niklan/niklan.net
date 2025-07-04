<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Heading;

use Drupal\external_content\Contract\Parser\Html\Parser;
use Drupal\external_content\Domain\HeadingTagType;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Parser\Html\HtmlParseRequest;

final class HtmlParser implements Parser {

  public function supports(HtmlParseRequest $request): bool {
    if (!$request->currentHtmlNode instanceof \DOMElement) {
      return FALSE;
    }
    return \in_array($request->currentHtmlNode->nodeName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']);
  }

  public function parse(HtmlParseRequest $request): Node {
    \assert($request->currentHtmlNode instanceof \DOMElement);
    $heading = new Heading(HeadingTagType::fromHtmlTag($request->currentHtmlNode->nodeName));
    $request->importRequest->getHtmlParser()->parseChildren($request->withNewContentNode($heading));

    return $heading;
  }

}
