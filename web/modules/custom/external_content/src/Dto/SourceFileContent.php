<?php

declare(strict_types=1);

namespace Drupal\external_content\Dto;

/**
 * Represents a parsed and structured source file content.
 */
final class SourceFileContent {

  /**
   * The content elements.
   *
   * @var \Drupal\external_content\Dto\ElementInterface[]
   */
  protected array $elements = [];

  /**
   * Adds content element.
   *
   * @param \Drupal\external_content\Dto\ElementInterface $element
   *   The content element.
   *
   * @return $this
   */
  public function addElement(ElementInterface $element): self {
    $this->elements[] = $element;
    return $this;
  }

  /**
   * Gets elements.
   *
   * @return \ArrayIterator
   *   An array iterator for elements.
   */
  public function getElements(): \ArrayIterator {
    return new \ArrayIterator($this->elements);
  }

}
