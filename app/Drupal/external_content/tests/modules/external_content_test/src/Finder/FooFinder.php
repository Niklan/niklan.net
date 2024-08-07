<?php

declare(strict_types=1);

namespace Drupal\external_content_test\Finder;

use Drupal\external_content\Contract\Finder\FinderInterface;
use Drupal\external_content\Data\FinderResult;
use Drupal\external_content\Data\SourceCollection;
use Drupal\external_content\Source\File;

/**
 * Provides a finder with a predefined set of files.
 */
final class FooFinder implements FinderInterface {

  #[\Override]
  public function find(): FinderResult {
    $files = new SourceCollection();
    $files->add(new File('foo/bar', 'foo/bar/baz.txt', 'text'));

    return FinderResult::withSources($files);
  }

}
