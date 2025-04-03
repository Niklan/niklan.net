<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array\Parser;

use Drupal\external_content\Utils\Registry;

final readonly class ArrayParser {

  /**
   * @param \Drupal\external_content\Utils\Registry<\Drupal\external_content\Importer\Array\Parser\ArrayElementParser> $parsers
   */
  public function __construct(
    private Registry $parsers,
  ) {}

  public function parseChildren(ArrayParseRequest $parse_request): void {
    foreach ($parse_request->currentArrayElement->getChildren() as $child_array_element) {
      $this->parseChild($parse_request->withNewArrayElement($child_array_element));
    }
  }

  private function parseChild(ArrayParseRequest $parse_request): void {
    foreach ($this->parsers->getAll() as $parser) {
      if (!$parser->supports($parse_request)) {
        continue;
      }

      $parse_request->currentAstNode->addChild($parser->parse($parse_request));

      return;
    }

    $parse_request->importRequest->getContext()->getLogger()->error("No ArrayElement parser found for node: {$parse_request->currentArrayElement->type}");
  }

}
