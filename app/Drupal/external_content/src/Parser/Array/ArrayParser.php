<?php

declare(strict_types=1);

namespace Drupal\external_content\Parser\Array;

use Drupal\external_content\Contract\Parser\Array\Parser;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Nodes\Document;
use Drupal\external_content\Utils\Registry;

final readonly class ArrayParser {

  /**
   * @param \Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Parser\Array\Parser> $parsers
   */
  public function __construct(
    private Registry $parsers,
  ) {}

  public function parse(ArrayParseRequest $request): Document {
    $document = new Document();

    $array_element = ArrayElement::fromArray($request->getSource()->getSourceData());
    $parse_request = new ArrayParseRequest($array_element, $document, $request);
    $this->parseChildren($parse_request);

    return $document;
  }

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

    $parse_request->getContext()->getLogger()->warning('Missing parser for custom element', [
      'element_type' => $parse_request->currentArrayElement->type,
      'parser_type' => Parser::class,
    ]);
  }

}
