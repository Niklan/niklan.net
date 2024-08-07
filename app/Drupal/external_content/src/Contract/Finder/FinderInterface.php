<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Finder;

use Drupal\external_content\Data\FinderResult;

/**
 * Represents a specific external content finder.
 */
interface FinderInterface {

  public function find(): FinderResult;

}
