<?php

declare(strict_types = 1);

namespace Drupal\external_content\Dto;

use Drupal\external_content\Parser\ChainHtmlParserInterface;

/**
 * Represents a HTML parser state.
 */
final class HtmlParserState implements HtmlParserStateInterface {

  /**
   * Constructs a new HtmlParserState object.
   *
   * @param \Drupal\external_content\Dto\SourceFile $sourceFile
   *   The source file.
   * @param \Drupal\external_content\Dto\SourceFileParams $sourceFileParams
   *   The source file params.
   * @param \Drupal\external_content\Parser\ChainHtmlParserInterface $parser
   *   The chained HTML parser used with this state.
   */
  public function __construct(
    protected SourceFile $sourceFile,
    protected SourceFileParams $sourceFileParams,
    protected ChainHtmlParserInterface $parser,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getParser(): ChainHtmlParserInterface {
    return $this->parser;
  }

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

}
