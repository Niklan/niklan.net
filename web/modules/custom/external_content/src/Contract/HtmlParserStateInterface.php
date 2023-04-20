<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\SourceFile;
use Drupal\external_content\Data\SourceFileParams;

/**
 * Provides the HTML parser state DTO interface.
 */
interface HtmlParserStateInterface {

  /**
   * Gets chain HTML parser.
   *
   * @return \Drupal\external_content\Contract\ChainHtmlParserInterface
   *   The chained HTML parser.
   */
  public function getParser(): ChainHtmlParserInterface;

  /**
   * Gets source file.
   *
   * @return \Drupal\external_content\Data\SourceFile
   *   The source file.
   */
  public function getSourceFile(): SourceFile;

  /**
   * Gets source file params.
   *
   * @return \Drupal\external_content\Data\SourceFileParams
   *   The source file params.
   */
  public function getSourceFileParams(): SourceFileParams;

}
