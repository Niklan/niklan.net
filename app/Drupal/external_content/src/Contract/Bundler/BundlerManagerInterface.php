<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Bundler;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Data\SourceBundleCollection;
use Drupal\external_content\Data\SourceCollection;

/**
 * {@selfdoc}
 */
interface BundlerManagerInterface extends EnvironmentAwareInterface {

  /**
   * {@selfdoc}
   */
  public function bundle(SourceCollection $source_collection): SourceBundleCollection;

}
