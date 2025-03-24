<?php

declare(strict_types=1);

namespace Drupal\external_content\DataStructure\Nodes;

/**
 * An abstract node representing content structure leaf.
 *
 * Do not be confused, this is not an HTML node, this is a simple content leaf
 * for a specific purpose.
 */
abstract class ContentNode {

  protected ?self $parent = NULL;

  /**
   * @var array<self>
   */
  protected array $children = [];
  private ?RootNode $cachedRootNode = NULL;

  public function __construct(
    protected string $type,
  ) {}

  public function getType(): string {
    return $this->type;
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
  public function getRoot(): RootNode {
    if ($this->cachedRootNode) {
      return $this->cachedRootNode;
    }

    $node = $this;

    while ($node->hasParent()) {
      $node = $node->getParent();
    }

    if (!$node instanceof RootNode) {
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
  }

}
