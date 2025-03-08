<?php

declare(strict_types=1);

namespace Drupal\external_content\Node;

abstract class ContentNode {

  protected ?self $parent = NULL;

  /**
   * @var array<self>
   */
  protected array $children = [];

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

  public function replaceNode(self $search, self $replace): void {
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
  }

  public function getRoot(): RootNode {
    $node = $this;

    while ($node->hasParent()) {
      $node = $node->getParent();
    }

    if (!$node instanceof RootNode) {
      throw new \LogicException('Element does not have a root node.');
    }

    return $node;
  }

  /**
   * @phpstan-assert-if-true self $this->getParent()
   */
  public function hasParent(): bool {
    return !\is_null($this->parent);
  }

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
