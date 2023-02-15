<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Grouper;

use Drupal\Core\Plugin\PluginBase;
use Drupal\external_content\Dto\ExternalContentCollection;
use Drupal\external_content\Dto\ParsedSourceFile;
use Drupal\external_content\Dto\ParsedSourceFileCollection;

/**
 * Provides a base implementation for grouping content.
 */
abstract class GrouperPluginBase extends PluginBase implements GrouperInterface {

  /**
   * {@inheritdoc}
   */
  public function group(ParsedSourceFileCollection $parsed_files): ExternalContentCollection {
    $collection = new ExternalContentCollection();

    foreach ($parsed_files as $parsed_file) {
      if (!$this->isApplicable($parsed_file)) {
        continue;
      }

      $this->doGroup($parsed_file, $collection);
    }

    return $collection;
  }

  /**
   * Checks is parsed file can be processed by current grouper.
   *
   * @param \Drupal\external_content\Dto\ParsedSourceFile $parsed_file
   *   The parsed source file.
   *
   * @return bool
   *   TRUE if this file can be processed.
   */
  abstract protected static function isApplicable(ParsedSourceFile $parsed_file): bool;

  /**
   * Groups a file.
   *
   * @param \Drupal\external_content\Dto\ParsedSourceFile $parsed_file
   *   The parsed source file.
   * @param \Drupal\external_content\Dto\ExternalContentCollection $collection
   *   The external content collection.
   */
  abstract protected function doGroup(ParsedSourceFile $parsed_file, ExternalContentCollection $collection): void;

}
