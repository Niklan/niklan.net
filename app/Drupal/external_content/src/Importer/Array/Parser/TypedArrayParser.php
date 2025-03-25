<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Importer\Array\Parser;

use Drupal\external_content\Contract\DataStructure\ArrayElementParser;
use Drupal\external_content\DataStructure\Nodes\ContentNode;
use Drupal\external_content\Importer\Array\Parser\ArrayParseRequest;

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
  public function registerType(string $elementType, string $node_class): void {
    $this->typeMapping[$elementType] = $node_class;
  }

  public function supports(ArrayParseRequest $request): bool {
    return isset($this->typeMapping[$request->currentArrayElement->type]);
  }

  protected function createNode(string $elementType): ContentNode {
    $nodeClass = $this->typeMapping[$elementType];

    return new $nodeClass();
  }

}
