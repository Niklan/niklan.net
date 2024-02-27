<?php declare(strict_types = 1);

namespace Drupal\external_content\Bundler;

use Drupal\external_content\Contract\Bundler\BundlerManagerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Identifier\IdentifierInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\SourceBundle;
use Drupal\external_content\Data\SourceBundleCollection;
use Drupal\external_content\Data\SourceCollection;
use Drupal\external_content\Data\SourceVariation;

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
  public function bundle(SourceCollection $source_collection): SourceBundleCollection {
    $identified_bundles = [];
    $sources = $source_collection->getIterator()->getArrayCopy();

    foreach ($this->environment->getIdentifiers() as $identifier) {
      \assert($identifier instanceof IdentifierInterface);
      $this->identifyBundles($identifier, $sources, $identified_bundles);
    }

    return $this->packBundles($identified_bundles);
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
  protected function identifyBundles(IdentifierInterface $identifier, array $sources, array &$identified_bundles): void {
    foreach ($sources as $source) {
      \assert($source instanceof SourceInterface);

      if (!$identifier->supportsIdentification($source)) {
        continue;
      }

      $result = $identifier->identify($source);
      $identified_bundles[$result->id][] = [
        'source' => $source,
        'attributes' => $result->attributes,
      ];
    }
  }

  /**
   * Creates bundles and packs them into collection.
   */
  protected function packBundles(array $identified_bundles): SourceBundleCollection {
    $bundle_collection = new SourceBundleCollection();

    foreach ($identified_bundles as $id => $variations) {
      $bundle = new SourceBundle((string) $id);

      foreach ($variations as $variation) {
        $bundle_variant = new SourceVariation(
          $variation['source'],
          $variation['attributes'],
        );
        $bundle->add($bundle_variant);
      }

      $bundle_collection->add($bundle);
    }

    return $bundle_collection;
  }

}
