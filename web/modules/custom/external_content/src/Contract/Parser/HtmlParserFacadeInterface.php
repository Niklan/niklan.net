<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Parser;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Node\ExternalContentDocument;

/**
 * Represents an external content HTML parser.
 */
interface HtmlParserFacadeInterface extends EnvironmentAwareInterface {

  /**
   * Parses the external content HTML into AST.
   *
   * @param \Drupal\external_content\Data\ExternalContentFile $file
   *   The external content file.
   */
  public function parse(ExternalContentFile $file): ExternalContentDocument;

}
