<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Plugin\ExternalContent\Grouper;

use Drupal\external_content\Data\ExternalContentCollection;
use Drupal\external_content\Data\ParsedSourceFile;
use Drupal\external_content\Plugin\ExternalContent\Grouper\GrouperPlugin;

/**
 * Provides grouper which is never applicable.
 *
 * @ExternalContentGrouper(
 *   id = "false",
 *   label = @Translation("Always false"),
 * )
 */
final class FalseGrouper extends GrouperPlugin {

  /**
   * {@inheritdoc}
   */
  protected static function isApplicable(ParsedSourceFile $parsed_file): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  protected function doGroup(ParsedSourceFile $parsed_file, ExternalContentCollection $collection): void {
    // It shouldn't be ever called.
  }

}
