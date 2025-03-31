<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array\Parser;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\DataStructure\Nodes\ContentNode;

/**
 * @template T of \Drupal\external_content\DataStructure\Nodes\ContentNode
 */
abstract class TypedArrayParser implements ArrayElementParser {

  /**
   * @var array<string, class-string<T>>
   */
  protected array $typeMapping = [];

  /**
   * @param class-string<T> $node_class
   */
  public function registerType(string $element_type, string $node_class): void {
    $this->typeMapping[$element_type] = $node_class;
  }

  public function supports(ArrayParseRequest $request): bool {
    return isset($this->typeMapping[$request->currentArrayElement->type]);
  }

  protected function createNode(string $element_type, array $arguments = []): ContentNode {
    $node_class = $this->typeMapping[$element_type];

    return new $node_class(...$arguments);
  }

}
