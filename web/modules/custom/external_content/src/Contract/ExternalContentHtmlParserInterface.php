<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContentHtml;
use Drupal\external_content\Node\ExternalContentDocument;

/**
 * Represents an external content HTML parser.
 */
interface ExternalContentHtmlParserInterface extends EnvironmentAwareInterface {

  /**
   * Parses the external content HTML into AST.
   *
   * @param \Drupal\external_content\Data\ExternalContentHtml $html
   *   The external content HTML.
   *
   * @return \Drupal\external_content\Node\ExternalContentDocument
   *   The external content document AST.
   */
  public function parse(ExternalContentHtml $html): ExternalContentDocument;

}
