<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Bundler;

use Drupal\external_content\Data\BundlerResult;
use Drupal\external_content\Data\IdentifiedSource;

interface BundlerInterface {

  public function bundle(IdentifiedSource $identified_source): BundlerResult;

}
