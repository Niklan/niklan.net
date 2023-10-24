<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Finder;

use Drupal\external_content\Source\Collection;

/**
 * Represents a specific external content finder.
 */
interface FinderInterface {

  /**
   * {@selfdoc}
   */
  public function find(): Collection;

}
