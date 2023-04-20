<?php declare(strict_types = 1);

namespace Drupal\external_content\Data;

use Drupal\external_content\Contract\ElementInterface;

/**
 * Represents a parsed and structured source file content.
 */
final class SourceFileContent extends Element {

  /**
   * {@inheritdoc}
   */
  public function setParent(ElementInterface $element): ElementInterface {
    // SourceFileContent is a root element that can't have parent.
    return $this;
  }

}
