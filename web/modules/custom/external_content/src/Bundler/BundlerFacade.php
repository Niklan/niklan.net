<?php declare(strict_types = 1);

namespace Drupal\external_content\Bundler;

use Drupal\external_content\Contract\Bundler\BundlerFacadeInterface;
use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Bundler\BundlerResultIdentifiedInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\ExternalContentBundleCollection;
use Drupal\external_content\Data\SourceBundle;
use Drupal\external_content\Data\SourceCollection;
use Drupal\external_content\Data\SourceVariant;

/**
 * Provides an external content bundler.
 */
final class BundlerFacade implements BundlerFacadeInterface {

  /**
   * The environment.
   */
  protected EnvironmentInterface $environment;

  /**
   * {@inheritdoc}
   */
  public function bundle(SourceCollection $source_collection): ExternalContentBundleCollection {
    $identified_bundles = [];
    $sources = $source_collection->getIterator()->getArrayCopy();

    foreach ($this->environment->getBundlers() as $bundler) {
      \assert($bundler instanceof BundlerInterface);
      // Let each bundler go over whole sources before passing it to the
      // other.
      $this->identifyBundles($bundler, $sources, $identified_bundles);
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
   * Identifies bundles using specific bundler.
   */
  protected function identifyBundles(BundlerInterface $instance, array $sources, array &$identified_bundles): void {
    foreach ($sources as $key => $source) {
      \assert($source instanceof SourceInterface);
      $result = $instance->bundle($source);

      if ($result->isUnidentified()) {
        continue;
      }

      \assert($result instanceof BundlerResultIdentifiedInterface);
      unset($sources[$key]);

      $identified_bundles[$result->id()][] = [
        'source' => $source,
        'attributes' => $result->attributes(),
      ];
    }
  }

  /**
   * Creates bundles and packs them into collection.
   */
  protected function packBundles(array $identified_bundles): ExternalContentBundleCollection {
    $bundle_collection = new ExternalContentBundleCollection();

    foreach ($identified_bundles as $id => $bundle_documents) {
      $bundle = new SourceBundle((string) $id);

      foreach ($bundle_documents as $bundle_document) {
        $bundle_variant = new SourceVariant(
          $bundle_document['source'],
          $bundle_document['attributes'],
        );
        $bundle->add($bundle_variant);
      }

      $bundle_collection->add($bundle);
    }

    return $bundle_collection;
  }

}
