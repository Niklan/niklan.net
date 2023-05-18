<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Plugin\ExternalContent\Source;

use Drupal\external_content\Plugin\ExternalContent\Source\SourcePlugin;

/**
 * Provides a 'foo' source plugin for testing.
 *
 * @ExternalContentSource(
 *   id = "foo",
 * )
 */
final class FooSource extends SourcePlugin {

  /**
   * {@inheritdoc}
   */
  public function workingDir(): string {
    return 'public://foo';
  }

}
