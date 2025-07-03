<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes;

/**
 * An abstract node representing content structure leaf.
 *
 * Do not be confused, this is not an HTML node, this is a simple content leaf
 * for a specific purpose.
 */
abstract class Node {

  protected ?self $parent = NULL;

  /**
   * @var array<self>
   */
  protected array $children = [];
  private ?Document $cachedDocument = NULL;

  abstract public static function getNodeType(): string;

  public function addChild(self $node): void {
    $node->setParent($this);
    $this->children[] = $node;
  }

  /**
   * @return array<self>
   */
  public function getChildren(): array {
    return $this->children;
  }

  public function hasChildren(): bool {
    return (bool) \count($this->children);
  }

  /**
   * @throws \LogicException
   */
  public function getDocument(): Document {
    if ($this->cachedDocument) {
      return $this->cachedDocument;
    }

    $node = $this;
    while ($node->hasParent()) {
      $node = $node->getParent();
    }

    if (!$node instanceof Document) {
      throw new \LogicException('Element does not have a document node.');
    }

    $this->cachedDocument = $node;
    return $node;
  }

  /**
   * @phpstan-assert-if-true self $this->parent
   */
  public function hasParent(): bool {
    return !\is_null($this->parent);
  }

  /**
   * @throws \LogicException
   */
  public function getParent(): self {
    if (!$this->hasParent()) {
      throw new \LogicException('Trying to request parent on a node without a parent.');
    }

    return $this->parent;
  }

  public function setParent(self $node): void {
    $this->parent = $node;
    $this->resetDocumentCacheRecursively();
  }

  public function replaceChild(self $old, self $new): void {
    $index = \array_search($old, $this->children, TRUE);
    if ($index === FALSE) {
      throw new \InvalidArgumentException('The old node is not a child of this node.');
    }

    $this->children[$index] = $new;
    $old->parent = NULL;
    $old->resetDocumentCacheRecursively();
    $new->setParent($this);
  }

  public function removeChild(self $child): void {
    $index = \array_search($child, $this->children, TRUE);
    if ($index === FALSE) {
      throw new \InvalidArgumentException('The node is not a child of this node.');
    }

    unset($this->children[$index]);
    $this->children = \array_values($this->children);
    $child->parent = NULL;
    $child->resetDocumentCacheRecursively();
  }

  private function resetDocumentCacheRecursively(): void {
    $this->cachedDocument = NULL;
    foreach ($this->children as $child) {
      $child->resetDocumentCacheRecursively();
    }
  }

}
