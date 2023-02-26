<?php declare(strict_types = 1);

namespace Drupal\external_content\Dto;

/**
 * Provides an abstract implementation for content block.
 */
abstract class Element implements ElementInterface {

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
  public function replaceElement(ElementInterface $search, ElementInterface $replace): self {
    foreach ($this->children as &$child) {
      if ($child === $search) {
        $replace->setParent($this);
        $child = $replace;
      }
      else {
        // If current element is not suitable, also check it children.
        $child->replaceElement($search, $replace);
      }
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRoot(): ElementInterface {
    if (!$this->hasParent()) {
      return $this;
    }

    $element = $this;

    while ($element->hasParent()) {
      $element = $element->getParent();
    }

    return $element;
  }

}
