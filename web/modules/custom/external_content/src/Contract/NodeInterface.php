<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

/**
 * Provides interface for single content node.
 *
 * The content node — a single typed content data. E.g., image, text, code.
 */
interface NodeInterface {

  /**
   * Sets parent node if current is child.
   *
   * @param self $node
   *   The parent node.
   *
   * @return $this
   */
  public function setParent(self $node): self;

  /**
   * Checks a current node for a parent.
   *
   * @return bool
   *   TRUE if has a parent, FALSE otherwise.
   */
  public function hasParent(): bool;

  /**
   * Gets parent node.
   *
   * @return self|null
   *   The parent node.
   */
  public function getParent(): ?self;

  /**
   * Adds child node.
   *
   * @param self $node
   *   The child node.
   *
   * @return $this
   */
  public function addChild(self $node): self;

  /**
   * Gets children nodes.
   *
   * @return \ArrayIterator
   *   An array with children.
   */
  public function getChildren(): \ArrayIterator;

  /**
   * Checks is a current node has children.
   *
   * @return bool
   *   TRUE if node have children.
   */
  public function hasChildren(): bool;

  /**
   * Replaces one node with another.
   *
   * @param self $search
   *   The node to replace.
   * @param self $replace
   *   The node to replace by.
   *
   * @return $this
   */
  public function replaceNode(self $search, self $replace): self;

  /**
   * Gets the root node.
   *
   * If node doesn't have parent node, that mens it is root node.
   *
   * @return self
   *   The root node.
   */
  public function getRoot(): self;

}
