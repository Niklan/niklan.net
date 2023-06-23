<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Parser;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
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
   */
  public function parse(ExternalContentHtml $html): ExternalContentDocument;

}
