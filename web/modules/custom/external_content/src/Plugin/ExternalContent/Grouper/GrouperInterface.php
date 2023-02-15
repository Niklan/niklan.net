<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Grouper;

use Drupal\external_content\Dto\ExternalContentCollection;
use Drupal\external_content\Dto\ParsedSourceFileCollection;

/**
 * Provides a grouper plugin interface.
 *
 * Grouper plugins is responsible for grouping parsed files into single external
 * content representation with a different translations.
 *
 * E.g. You have two files for same content:
 * - content/
 *   - /en
 *     - /foo.md
 *   - /ru
 *     - /foo.md
 * They are the same, but in different languages. This plugin should combine
 * them into single 'external content' content with multiple translations (en,
 * ru).
 */
interface GrouperInterface {

  /**
   * Groups parsed files into content collections.
   *
   * @param \Drupal\external_content\Dto\ParsedSourceFileCollection $parsed_files
   *   A collection of parsed files.
   *
   * @return \Drupal\external_content\Dto\ExternalContentCollection
   *   A collection with grouped content.
   */
  public function group(ParsedSourceFileCollection $parsed_files): ExternalContentCollection;

}
