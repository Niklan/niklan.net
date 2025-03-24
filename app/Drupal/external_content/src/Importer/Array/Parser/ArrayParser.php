<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array\Parser;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\Utils\PrioritizedList;

final readonly class ArrayParser {

  /**
   * @var \Drupal\external_content\Utils\PrioritizedList<\Drupal\external_content\Contract\DataStructure\ArrayElementParser>
   */
  private PrioritizedList $parsers;

  public function __construct() {
    $this->parsers = new PrioritizedList();
  }

  public function addParser(ArrayElementParser $parser, int $priority = 0): void {
    $this->parsers->add($parser, $priority);
  }

  public function parseChildren(ArrayParseRequest $parse_request): void {
    foreach ($parse_request->currentArrayElement->getChildren() as $child_array_element) {
      $this->parseChild($parse_request->withNewArrayElement($child_array_element));
    }
  }

  private function parseChild(ArrayParseRequest $parse_request): void {
    foreach ($this->parsers as $parser) {
      if (!$parser->supports($parse_request)) {
        continue;
      }

      $parse_request->currentAstNode->addChild($parser->parse($parse_request));

      return;
    }

    $parse_request->importRequest->getContext()->getLogger()->error("No ArrayElement parser found for node: {$parse_request->currentArrayElement->nodeName}");
  }

}
