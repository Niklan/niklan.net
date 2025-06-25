<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Content;

use Drupal\external_content\DataStructure\NodeProperties;
use Drupal\external_content\Nodes\Root;

/**
 * An abstract node representing content structure leaf.
 *
 * Do not be confused, this is not an HTML node, this is a simple content leaf
 * for a specific purpose.
 */
abstract class Content {

  protected ?self $parent = NULL;

  /**
   * @var array<self>
   */
  protected array $children = [];
  protected NodeProperties $properties;
  private ?Root $cachedRootNode = NULL;

  abstract public static function getType(): string;

  public function __construct() {
    $this->properties = new NodeProperties();
  }

  public function getProperties(): NodeProperties {
    return $this->properties;
  }

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
  public function getRoot(): Root {
    if ($this->cachedRootNode) {
      return $this->cachedRootNode;
    }

    $node = $this;
    while ($node->hasParent()) {
      $node = $node->getParent();
    }

    if (!$node instanceof Root) {
      throw new \LogicException('Element does not have a root node.');
    }

    $this->cachedRootNode = $node;
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
    $this->resetRootCacheRecursively();
  }

  public function replaceChild(self $old, self $new): void {
    $index = \array_search($old, $this->children, TRUE);
    if ($index === FALSE) {
      throw new \InvalidArgumentException('The old node is not a child of this node.');
    }

    $this->children[$index] = $new;
    $old->parent = NULL;
    $old->resetRootCacheRecursively();
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
    $child->resetRootCacheRecursively();
  }

  private function resetRootCacheRecursively(): void {
    $this->cachedRootNode = NULL;
    foreach ($this->children as $child) {
      $child->resetRootCacheRecursively();
    }
  }

}
