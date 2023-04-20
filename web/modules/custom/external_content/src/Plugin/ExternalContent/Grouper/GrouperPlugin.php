<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Grouper;

use Drupal\Core\Plugin\PluginBase;
use Drupal\external_content\Contract\GrouperPluginInterface;
use Drupal\external_content\Data\ExternalContentCollection;
use Drupal\external_content\Data\ParsedSourceFile;
use Drupal\external_content\Data\ParsedSourceFileCollection;

/**
 * Provides a base implementation for grouping content.
 */
abstract class GrouperPlugin extends PluginBase implements GrouperPluginInterface {

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
   * @param \Drupal\external_content\Data\ParsedSourceFile $parsed_file
   *   The parsed source file.
   *
   * @return bool
   *   TRUE if this file can be processed.
   */
  abstract protected static function isApplicable(ParsedSourceFile $parsed_file): bool;

  /**
   * Groups a file.
   *
   * @param \Drupal\external_content\Data\ParsedSourceFile $parsed_file
   *   The parsed source file.
   * @param \Drupal\external_content\Data\ExternalContentCollection $collection
   *   The external content collection.
   */
  abstract protected function doGroup(ParsedSourceFile $parsed_file, ExternalContentCollection $collection): void;

}
