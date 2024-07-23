<?php

declare(strict_types=1);

namespace Drupal\external_content\Node;

use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * Provides an abstract implementation for content block.
 */
abstract class Node implements NodeInterface {

  protected ?NodeInterface $parent = NULL;
  protected array $children = [];

  #[\Override]
  public function addChild(NodeInterface $node): NodeInterface {
    $node->setParent($this);
    $this->children[] = $node;

    return $this;
  }

  #[\Override]
  public function addChildren(NodeList $node_list): NodeInterface {
    foreach ($node_list->getChildren() as $node) {
      $this->addChild($node);
    }

    return $this;
  }

  #[\Override]
  public function getChildren(): \ArrayIterator {
    return new \ArrayIterator($this->children);
  }

  #[\Override]
  public function hasChildren(): bool {
    return (bool) \count($this->children);
  }

  #[\Override]
  public function replaceNode(NodeInterface $search, NodeInterface $replace): self {
    foreach ($this->children as &$child) {
      if ($child === $search) {
        $replace->setParent($this);
        $child = $replace;
      }
      else {
        // If current element is not suitable, also check it children.
        $child->replaceNode($search, $replace);
      }
    }

    return $this;
  }

  #[\Override]
  public function getRoot(): NodeInterface {
    if (!$this->hasParent()) {
      return $this;
    }

    $node = $this;

    while ($node->hasParent()) {
      $node = $node->getParent();
    }

    return $node;
  }

  #[\Override]
  public function hasParent(): bool {
    return !\is_null($this->parent);
  }

  #[\Override]
  public function getParent(): ?NodeInterface {
    return $this->parent;
  }

  #[\Override]
  public function setParent(NodeInterface $node): NodeInterface {
    $this->parent = $node;

    return $this;
  }

}
