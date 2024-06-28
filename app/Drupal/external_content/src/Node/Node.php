<?php

declare(strict_types=1);

namespace Drupal\external_content\Node;

use Drupal\external_content\Contract\Node\NodeInterface;

/**
 * Provides an abstract implementation for content block.
 */
abstract class Node implements NodeInterface {

  /**
   * {@selfdoc}
   */
  protected ?NodeInterface $parent = NULL;

  /**
   * {@selfdoc}
   */
  protected array $children = [];

  /**
   * {@inheritdoc}
   */
  public function addChild(NodeInterface $node): NodeInterface {
    $node->setParent($this);
    $this->children[] = $node;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addChildren(NodeList $node_list): NodeInterface {
    foreach ($node_list->getChildren() as $node) {
      $this->addChild($node);
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChildren(): \ArrayIterator {
    return new \ArrayIterator($this->children);
  }

  /**
   * {@inheritdoc}
   */
  public function hasChildren(): bool {
    return (bool) \count($this->children);
  }

  /**
   * {@inheritdoc}
   */
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

  /**
   * {@inheritdoc}
   */
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

  /**
   * {@inheritdoc}
   */
  public function hasParent(): bool {
    return !\is_null($this->parent);
  }

  /**
   * {@inheritdoc}
   */
  public function getParent(): ?NodeInterface {
    return $this->parent;
  }

  /**
   * {@inheritdoc}
   */
  public function setParent(NodeInterface $node): NodeInterface {
    $this->parent = $node;

    return $this;
  }

}
