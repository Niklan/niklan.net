<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Bundler;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Data\IdentifiedSourceBundleCollection;
use Drupal\external_content\Data\IdentifiedSourceCollection;

/**
 * {@selfdoc}
 */
interface BundlerManagerInterface extends EnvironmentAwareInterface {

  /**
   * {@selfdoc}
   */
  public function bundle(IdentifiedSourceCollection $source_collection): IdentifiedSourceBundleCollection;

}
