<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContentCollection;
use Drupal\external_content\Data\SourceConfiguration;

/**
 * Provides an external content finder.
 *
 * This finder wraps other subsystems responsible for finding source files,
 * parse their content, covert their markup into HTML and from HTML into
 * structured typed objects with content elements. At last, this finder will
 * group found source files into external content typed objects.
 *
 * Pipeline:
 * - Source file finder.
 * - Source file parser.
 *   - Converting markup into HTML.
 *   - Parse HTML into typed elements.
 * - Source file grouper.
 */
interface ExternalContentFinderInterface {

  /**
   * Finds external content.
   *
   * @param \Drupal\external_content\Data\SourceConfiguration $source
   *   The external content source.
   *
   * @return \Drupal\external_content\Data\ExternalContentCollection
   *   The found external content.
   */
  public function find(SourceConfiguration $source): ExternalContentCollection;

}
