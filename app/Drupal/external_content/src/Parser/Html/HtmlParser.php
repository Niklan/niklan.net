<?php

declare(strict_types=1);

namespace Drupal\external_content\Parser\Html;

use Drupal\external_content\Utils\Registry;

final readonly class HtmlParser {

  /**
   * @param \Drupal\external_content\Utils\Registry<\Drupal\external_content\Importer\Html\Parser\HtmlNodeParser> $parsers
   */
  public function __construct(
    private Registry $parsers,
  ) {}

  public function parseChildren(HtmlParseRequest $parse_request): void {
    foreach ($parse_request->currentHtmlNode->childNodes as $child_html_node) {
      $this->parseChild($parse_request->withNewHtmlNode($child_html_node));
    }
  }

  private function parseChild(HtmlParseRequest $parse_request): void {
    foreach ($this->parsers->getAll() as $parser) {
      if (!$parser->supports($parse_request)) {
        continue;
      }

      $parse_request->currentAstNode->addChild($parser->parse($parse_request));
      return;
    }

    $parse_request
      ->importRequest
      ->getContext()
      ->getLogger()
      ->debug('No HTML parser found for node.', [
        'node_name' => $parse_request->currentHtmlNode->nodeName,
        'content' => self::domNodeToString($parse_request->currentHtmlNode),
      ]);
  }

  private static function domNodeToString(\DOMNode $node): string {
    $document = new \DOMDocument();
    $cloned_node = $document->importNode($node->cloneNode(TRUE), TRUE);
    $document->appendChild($cloned_node);
    $content = \trim($document->saveHTML());

    return $content === '' ? '[empty]' : $content;
  }

}
