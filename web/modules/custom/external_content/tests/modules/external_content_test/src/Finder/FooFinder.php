<?php declare(strict_types = 1);

namespace Drupal\external_content_test\Finder;

use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Data\ExternalContentFileCollection;

/**
 * Provides a finder with a predefined set of files.
 */
final class FooFinder implements FinderInterface {

  /**
   * {@inheritdoc}
   */
  public function find(): ExternalContentFileCollection {
    $files = new ExternalContentFileCollection();
    $files->add(new ExternalContentFile('foo/bar', 'foo/bar/baz.txt'));

    return $files;
  }

}
