<?php

declare(strict_types=1);

namespace Drupal\external_content\Bundler;

use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Data\BundlerResult;
use Drupal\external_content\Data\IdentifiedSource;

final class SameIdBundler implements BundlerInterface {

  #[\Override]
  public function bundle(IdentifiedSource $identified_source): BundlerResult {
    return BundlerResult::bundleAs($identified_source->id);
  }

}
