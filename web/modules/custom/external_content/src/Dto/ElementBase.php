<?php

declare(strict_types=1);

namespace Drupal\external_content\Dto;

/**
 * Provides an abstract implementation for content block.
 */
abstract class ElementBase implements ElementInterface {

  /**
   * The parent element.
   */
  protected ?ElementInterface $parent = NULL;

  /**
   * An array with children.
   *
   * @var \Drupal\external_content\Dto\ElementInterface[]
   */
  protected array $children = [];

  /**
   * {@inheritdoc}
   */
  public function hasParent(): bool {
    return !\is_null($this->parent);
  }

  /**
   * {@inheritdoc}
   */
  public function getParent(): ?ElementInterface {
    return $this->parent;
  }

  /**
   * {@inheritdoc}
   */
  public function setParent(ElementInterface $element): ElementInterface {
    $this->parent = $element;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addChild(ElementInterface $element): ElementInterface {
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

}
