<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html\Parser;

use Drupal\external_content\Contract\Importer\HtmlNodeParser;
use Drupal\external_content\Utils\PrioritizedList;

final readonly class HtmlParser {

  /**
   * @var \Drupal\external_content\Utils\PrioritizedList<\Drupal\external_content\Contract\Importer\HtmlNodeParser>
   */
  private PrioritizedList $parsers;

  public function __construct() {
    $this->parsers = new PrioritizedList();
  }

  public function addParser(HtmlNodeParser $parser, int $priority = 0): void {
    $this->parsers->add($parser, $priority);
  }

  public function parseChildren(HtmlParseRequest $parse_request): void {
    foreach ($parse_request->currentHtmlNode->childNodes as $child_html_node) {
      $this->parseChild($parse_request->withNewHtmlNode($child_html_node));
    }
  }

  private function parseChild(HtmlParseRequest $parse_request): void {
    foreach ($this->parsers as $parser) {
      if (!$parser->supports($parse_request)) {
        continue;
      }

      $parse_request->currentAstNode->addChild($parser->parse($parse_request));

      return;
    }

    $parse_request->importRequest->getContext()->getLogger()->error("No HTML parser found for node: {$parse_request->currentHtmlNode->nodeName}");
  }

}
