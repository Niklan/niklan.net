<?php declare(strict_types = 1);

namespace Drupal\external_content\Bundler;

use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Bundler\BundlerManagerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\IdentifiedSource;
use Drupal\external_content\Data\IdentifiedSourceBundle;
use Drupal\external_content\Data\IdentifiedSourceBundleCollection;
use Drupal\external_content\Data\IdentifiedSourceCollection;

/**
 * Provides an external content bundler.
 */
final class BundlerManager implements BundlerManagerInterface {

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function bundle(IdentifiedSourceCollection $source_collection): IdentifiedSourceBundleCollection {
    $bundles = [];

    foreach ($source_collection->sources() as $source) {
      \assert($source instanceof IdentifiedSource);
      $this->bundleSource($source, $bundles);
    }

    return $this->packBundles($bundles);
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

  /**
   * {@selfdoc}
   */
  protected function packBundles(array $identified_bundles): IdentifiedSourceBundleCollection {
    $bundle_collection = new IdentifiedSourceBundleCollection();

    foreach ($identified_bundles as $id => $sources) {
      $bundle = new IdentifiedSourceBundle($id);

      foreach ($sources as $source) {
        $bundle->add($source);
      }

      $bundle_collection->add($bundle);
    }

    return $bundle_collection;
  }

  /**
   * {@selfdoc}
   */
  private function bundleSource(IdentifiedSource $source, array &$bundles): void {
    foreach ($this->environment->getBundlers() as $bundler) {
      \assert($bundler instanceof BundlerInterface);
      $result = $bundler->bundle($source);

      if ($result->shouldNotBeBundled()) {
        continue;
      }

      $bundles[$result->bundleId][] = $source;
    }
  }

}
