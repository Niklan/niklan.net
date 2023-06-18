<?php declare(strict_types = 1);

namespace Drupal\external_content\Node;

use Drupal\external_content\Contract\NodeInterface;

/**
 * Provides an abstract implementation for content block.
 */
abstract class Node implements NodeInterface {

  /**
   * The parent element.
   */
  protected ?NodeInterface $parent = NULL;

  /**
   * An array with children.
   *
   * @var \Drupal\external_content\Contract\NodeInterface[]
   */
  protected array $children = [];

  /**
   * {@inheritdoc}
   */
  public function addChild(NodeInterface $element): NodeInterface {
    $element->setParent($this);
    $this->children[] = $element;

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

    $element = $this;

    while ($element->hasParent()) {
      $element = $element->getParent();
    }

    return $element;
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
  public function setParent(NodeInterface $element): NodeInterface {
    $this->parent = $element;

    return $this;
  }

}
