<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

/**
 * Provides interface for single content element.
 *
 * The content element — a single typed content data. E.g., image, text, code.
 */
interface ElementInterface {

  /**
   * Sets parent element if current is child.
   *
   * @param self $element
   *   The parent element.
   *
   * @return $this
   */
  public function setParent(self $element): self;

  /**
   * Checks a current element for a parent.
   *
   * @return bool
   *   TRUE if has a parent, FALSE otherwise.
   */
  public function hasParent(): bool;

  /**
   * Gets parent element.
   *
   * @return self|null
   *   The parent element.
   */
  public function getParent(): ?self;

  /**
   * Adds child element.
   *
   * @param self $element
   *   The child element.
   *
   * @return $this
   */
  public function addChild(self $element): self;

  /**
   * Gets children elements.
   *
   * @return \ArrayIterator
   *   An array with children.
   */
  public function getChildren(): \ArrayIterator;

  /**
   * Checks is a current element has children.
   *
   * @return bool
   *   TRUE if element have children.
   */
  public function hasChildren(): bool;

  /**
   * Replaces one element with another.
   *
   * @param self $search
   *   The element to replace.
   * @param self $replace
   *   The element to replace by.
   *
   * @return $this
   */
  public function replaceElement(self $search, self $replace): self;

  /**
   * Gets the root element.
   *
   * If element doesn't have parent element, that mens it is root element.
   *
   * @return self
   *   The root element.
   */
  public function getRoot(): self;

}
