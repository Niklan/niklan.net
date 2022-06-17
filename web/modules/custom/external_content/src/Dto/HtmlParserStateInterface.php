<?php

declare(strict_types=1);

namespace Drupal\external_content\Dto;

/**
 * Provides the HTML parser state DTO interface.
 */
interface HtmlParserStateInterface {

  /**
   * Gets source file.
   *
   * @return \Drupal\external_content\Dto\SourceFile
   *   The source file.
   */
  public function getSourceFile(): SourceFile;

  /**
   * Gets source file params.
   *
   * @return \Drupal\external_content\Dto\SourceFileParams
   *   The source file params.
   */
  public function getSourceFileParams(): SourceFileParams;

  /**
   * Sets a parent element for currently parsed element.
   *
   * @param \Drupal\external_content\Dto\ElementInterface $element
   *   The element.
   *
   * @return $this
   */
  public function setParentElement(ElementInterface $element): self;

  /**
   * Gets a parent element.
   *
   * @return \Drupal\external_content\Dto\ElementInterface
   *   The parent element.
   */
  public function getParentElement(): ElementInterface;

}
