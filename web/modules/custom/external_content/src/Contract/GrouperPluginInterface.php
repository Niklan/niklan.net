<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContentCollection;
use Drupal\external_content\Data\ParsedSourceFileCollection;

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
interface GrouperPluginInterface {

  /**
   * Groups parsed files into content collections.
   *
   * @param \Drupal\external_content\Data\ParsedSourceFileCollection $parsed_files
   *   A collection of parsed files.
   *
   * @return \Drupal\external_content\Data\ExternalContentCollection
   *   A collection with grouped content.
   */
  public function group(ParsedSourceFileCollection $parsed_files): ExternalContentCollection;

}
