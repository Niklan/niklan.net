<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContentFileCollection;

/**
 * Provides an interface for a finder.
 */
interface FinderInterface {

  public function find(EnvironmentInterface $environment): ExternalContentFileCollection;

}
