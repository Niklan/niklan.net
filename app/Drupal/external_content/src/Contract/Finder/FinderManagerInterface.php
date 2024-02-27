<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Finder;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Data\SourceCollection;

/**
 * {@selfdoc}
 */
interface FinderManagerInterface extends EnvironmentAwareInterface {

  /**
   * {@selfdoc}
   */
  public function find(): SourceCollection;

}
