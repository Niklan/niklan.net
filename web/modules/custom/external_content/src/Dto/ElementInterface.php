<?php

declare(strict_types=1);

namespace Drupal\external_content\Dto;

/**
 * Provides interface for single content element.
 *
 * The content element — a single typed content data. E.g., image, text, code.
 */
interface ElementInterface {

  /**
   * Sets parent element if current is child.
   *
   * @param \Drupal\external_content\Dto\ElementInterface $element
   *   The parent element.
   *
   * @return $this
   */
  public function setParent(ElementInterface $element): self;

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
   * @return \Drupal\external_content\Dto\ElementInterface|null
   *   The parent element.
   */
  public function getParent(): ?ElementInterface;

  /**
   * Adds child element.
   *
   * @param \Drupal\external_content\Dto\ElementInterface $element
   *   The child element.
   *
   * @return $this
   */
  public function addChild(ElementInterface $element): self;

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

}
