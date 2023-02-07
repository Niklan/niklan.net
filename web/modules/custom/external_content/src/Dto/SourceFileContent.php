<?php

declare(strict_types=1);

namespace Drupal\external_content\Dto;

/**
 * Represents a parsed and structured source file content.
 */
final class SourceFileContent extends ElementBase {

  /**
   * {@inheritdoc}
   */
  public function setParent(ElementInterface $element): ElementInterface {
    // SourceFileContent is a root element that can't have parent.
    return $this;
  }

}
