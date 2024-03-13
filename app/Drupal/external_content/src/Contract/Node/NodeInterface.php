<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Node;

use Drupal\external_content\Node\NodeList;

/**
 * Represents a single external content AST node.
 *
 * The content node — a single typed content data. E.g., image, text, code.
 */
interface NodeInterface {

  /**
   * Sets parent node if current is child.
   *
   * @param self $node
   *   The parent node.
   */
  public function setParent(self $node): self;

  /**
   * Checks a current node for a parent.
   */
  public function hasParent(): bool;

  /**
   * Gets parent node.
   */
  public function getParent(): ?self;

  /**
   * Adds child node.
   *
   * @param self $node
   *   The child node.
   */
  public function addChild(self $node): self;

  /**
   * {@selfdoc}
   */
  public function addChildren(NodeList $node_list): self;

  /**
   * Gets children nodes.
   */
  public function getChildren(): \ArrayIterator;

  /**
   * Checks is a current node has children.
   */
  public function hasChildren(): bool;

  /**
   * Replaces one node with another.
   *
   * @param self $search
   *   The node to replace.
   * @param self $replace
   *   The node to replace by.
   */
  public function replaceNode(self $search, self $replace): self;

  /**
   * Gets the root node.
   *
   * If node doesn't have parent node, that mens it is root node.
   */
  public function getRoot(): self;

}
