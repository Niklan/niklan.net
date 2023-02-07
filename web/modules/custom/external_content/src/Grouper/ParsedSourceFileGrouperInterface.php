<?php

declare(strict_types=1);

namespace Drupal\external_content\Grouper;

use Drupal\external_content\Dto\ExternalContentCollection;
use Drupal\external_content\Dto\ParsedSourceFileCollection;

/**
 * Provides an interface for parsed source file collection groupers.
 */
interface ParsedSourceFileGrouperInterface {

  /**
   * Groups parsed source files into external content.
   *
   * @param \Drupal\external_content\Dto\ParsedSourceFileCollection $parsed_source_files
   *   The collection with parsed source files to group.
   * @param string $grouper_id
   *   The grouper plugin ID. Used default if non provided.
   *
   * @return \Drupal\external_content\Dto\ExternalContentCollection
   *   The collection with grouped external content.
   */
  public function group(ParsedSourceFileCollection $parsed_source_files, string $grouper_id = 'params'): ExternalContentCollection;

}
