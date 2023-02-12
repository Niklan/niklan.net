<?php

declare(strict_types = 1);

namespace Drupal\external_content\Dto;

use Drupal\external_content\Parser\ChainHtmlParserInterface;

/**
 * Provides the HTML parser state DTO interface.
 */
interface HtmlParserStateInterface {

  /**
   * Gets chain HTML parser.
   *
   * @return \Drupal\external_content\Parser\ChainHtmlParserInterface
   *   The chained HTML parser.
   */
  public function getParser(): ChainHtmlParserInterface;

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

}
