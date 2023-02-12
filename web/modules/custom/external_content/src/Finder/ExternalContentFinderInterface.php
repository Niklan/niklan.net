<?php

declare(strict_types = 1);

namespace Drupal\external_content\Finder;

use Drupal\external_content\Dto\ExternalContentCollection;
use Drupal\external_content\Plugin\ExternalContent\Configuration\Configuration;

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
   * @param \Drupal\external_content\Plugin\ExternalContent\Configuration\Configuration $configuration
   *   The external content configuration.
   *
   * @return \Drupal\external_content\Dto\ExternalContentCollection
   *   The found external content.
   */
  public function find(Configuration $configuration): ExternalContentCollection;

}
