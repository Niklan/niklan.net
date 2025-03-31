<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Array\Parser;

use Drupal\external_content\DataStructure\Nodes\CodeNode;
use Drupal\external_content\DataStructure\Nodes\ContentNode;
use Drupal\external_content\DataStructure\Nodes\ElementNode;
use Drupal\external_content\DataStructure\Nodes\FormatNode;
use Drupal\external_content\DataStructure\Nodes\HeadingNode;
use Drupal\external_content\DataStructure\Nodes\ImageNode;
use Drupal\external_content\DataStructure\Nodes\LinkNode;
use Drupal\external_content\DataStructure\Nodes\ListItemNode;
use Drupal\external_content\DataStructure\Nodes\ListNode;
use Drupal\external_content\DataStructure\Nodes\ParagraphNode;
use Drupal\external_content\DataStructure\Nodes\ThematicBreakNode;

final class ElementArrayParser extends TypedArrayParser {

  public function __construct() {
    $this->withDefaultTypes();
  }

  public function parse(ArrayParseRequest $request): ContentNode {
    $node = $this->createNode($request->currentArrayElement->type);
    \assert($node instanceof ElementNode);
    $this->applyAdditionalProperties($node, $request->currentArrayElement->properties);
    $request->importRequest->getArrayParser()->parseChildren($request->withNewContentNode($node));

    return $node;
  }

  private function withDefaultTypes(): void {
    $this->registerType(CodeNode::getType(), CodeNode::class);
    $this->registerType(FormatNode::getType(), FormatNode::class);
    $this->registerType(HeadingNode::getType(), HeadingNode::class);
    $this->registerType(ImageNode::getType(), ImageNode::class);
    $this->registerType(LinkNode::getType(), LinkNode::class);
    $this->registerType(ListItemNode::getType(), ListItemNode::class);
    $this->registerType(ListNode::getType(), ListNode::class);
    $this->registerType(ParagraphNode::getType(), ParagraphNode::class);
    $this->registerType(ThematicBreakNode::getType(), ThematicBreakNode::class);
  }

  private function applyAdditionalProperties(ElementNode $node, array $properties): void {
    foreach ($properties as $key => $value) {
      $node->getProperties()->setProperty($key, $value);
    }
  }

}
