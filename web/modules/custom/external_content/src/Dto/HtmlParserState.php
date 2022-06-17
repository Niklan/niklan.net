<?php

declare(strict_types=1);

namespace Drupal\external_content\Dto;

/**
 * Represents a HTML parser state.
 */
final class HtmlParserState implements HtmlParserStateInterface {

  /**
   * The parent element.
   *
   * @var \Drupal\external_content\Dto\ElementInterface
   */
  protected ElementInterface $parentElement;

  /**
   * Constructs a new HtmlParserState object.
   *
   * @param \Drupal\external_content\Dto\SourceFile $sourceFile
   *   The source file.
   * @param \Drupal\external_content\Dto\SourceFileParams $sourceFileParams
   *   The source file params.
   */
  public function __construct(
    protected SourceFile $sourceFile,
    protected SourceFileParams $sourceFileParams,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getSourceFile(): SourceFile {
    return $this->sourceFile;
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceFileParams(): SourceFileParams {
    return $this->sourceFileParams;
  }

  /**
   * {@inheritdoc}
   */
  public function getParentElement(): ElementInterface {
    return $this->parentElement;
  }

  /**
   * {@inheritdoc}
   */
  public function setParentElement(ElementInterface $element): HtmlParserStateInterface {
    $this->parentElement = $element;

    return $this;
  }

}
