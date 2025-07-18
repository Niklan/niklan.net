<?php

namespace Drupal\external_content\Builder\RenderArray;

use Drupal\external_content\Contract\Builder\RenderArray\Builder;
use Drupal\external_content\Contract\Builder\RenderArray\ChildBuilder;
use Drupal\external_content\DataStructure\RenderArray;
use Drupal\external_content\Exception\UnsupportedElementException;
use Drupal\external_content\Nodes\Document;
use Drupal\external_content\Nodes\Node;
use Drupal\external_content\Utils\Registry;

final class RenderArrayBuilder implements Builder, ChildBuilder {

  /**
   * @param \Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Builder\RenderArray\Builder> $builders
   */
  public function __construct(
    private Registry $builders,
  ) {}

  public function build(Document $document): RenderArray {
    $root = new RenderArray();
    $this->buildChildren($document, $root);
    return $root;
  }

  public function buildChildren(Node $parent_node, RenderArray $render_array): void {
    foreach ($parent_node->getChildren() as $child) {
      $render_array->addChild($this->buildElement($child, $this));
    }
  }

  public function buildElement(Node $node, ChildBuilder $child_builder): RenderArray {
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
