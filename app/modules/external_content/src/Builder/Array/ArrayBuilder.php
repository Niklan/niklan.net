<?php

namespace Drupal\external_content\Builder\Array;

use Drupal\external_content\Contract\Builder\Array\Builder;
use Drupal\external_content\Contract\Builder\Array\ChildBuilder;
use Drupal\external_content\DataStructure\ArrayElement;
use Drupal\external_content\Exception\UnsupportedElementException;
use Drupal\external_content\Nodes\Document;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Utils\Registry;

final readonly class ArrayBuilder implements Builder, ChildBuilder {

  /**
   * @param \Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Builder\Array\Builder> $builders
   */
  public function __construct(
    private Registry $builders,
  ) {}

  public function build(Document $document): ArrayElement {
    $array = new ArrayElement(Document::getNodeType());
    $this->buildChildren($document, $array);
    return $array;
  }

  public function buildChildren(Node $parent_node, ArrayElement $array): void {
    foreach ($parent_node->getChildren() as $child) {
      $array->addChild($this->buildElement($child, $this));
    }
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): ArrayElement {
    foreach ($this->builders->getAll() as $builder) {
      if (!$builder->supports($node)) {
        continue;
      }
      return $builder->buildElement($node, $child_builder);
    }

    throw new UnsupportedElementException(self::class, $node::getNodeType());
  }

  public function supports(Node $node): bool {
    return $node instanceof Document;
  }

}
