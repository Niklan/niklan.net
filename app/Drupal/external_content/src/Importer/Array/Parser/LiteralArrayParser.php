<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Importer\Array\Parser;

use Drupal\external_content\Contract\DataStructure\Node\LiteralAware;
use Drupal\external_content\DataStructure\Nodes\CodeNode;
use Drupal\external_content\DataStructure\Nodes\ContentNode;
use Drupal\external_content\DataStructure\Nodes\ElementNode;
use Drupal\external_content\DataStructure\Nodes\TextNode;
use Drupal\external_content\Importer\Array\Parser\ArrayParseRequest;

final class LiteralArrayParser extends TypedArrayParser {

  public function __construct() {
    $this->withDefaultTypes();
  }

  public function supports(ArrayParseRequest $request): bool {
    $has_literal = \array_key_exists('literal', $request->currentArrayElement->properties);
    $has_mapping = \array_key_exists($request->currentArrayElement->type, $this->typeMapping);

    return $has_literal && $has_mapping;
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    $node = $this->createNode($request->currentArrayElement->type);
    \assert($node instanceof LiteralAware);
    $node->setLiteral($request->currentArrayElement->properties['literal']);
    $this->applyAdditionalProperties($node, $request->currentArrayElement->properties);

    return $node;
  }

  private function applyAdditionalProperties(ContentNode $node, array $properties): void {
    if (!$node instanceof ElementNode) {
      return;
    }

    foreach ($properties as $key => $value) {
      if ($key === 'literal') {
        continue;
      }

      $node->getProperties()->setProperty($key, $value);
    }
  }

  private function withDefaultTypes(): void {
    $this->registerType('code', CodeNode::class);
    $this->registerType('text', TextNode::class);
  }

}
