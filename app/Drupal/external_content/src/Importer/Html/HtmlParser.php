<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

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

  public function parseChildren(HtmlParserRequest $request): void {
    foreach ($request->currentHtmlNode->childNodes as $child_html_node) {
      $this->parseChild($request->withNewHtmlNode($child_html_node));
    }
  }

  private function parseChild(HtmlParserRequest $request): void {
    foreach ($this->parsers as $transformer) {
      if (!$transformer->supports($request)) {
        continue;
      }

      $request->currentAstNode->addChild($transformer->parse($request));

      return;
    }

    $request->importRequest->getContext()->getLogger()->error("No HTML parser found for node: {$request->currentHtmlNode->nodeName}");
  }

}
