<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Parser;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Source\File;

/**
 * Represents an external content HTML parser.
 */
interface HtmlParserFacadeInterface extends EnvironmentAwareInterface {

  /**
   * Parses the external content HTML into AST.
   *
   * @param \Drupal\external_content\Source\File $file
   *   The external content file.
   */
  public function parse(File $file): Content;

}
