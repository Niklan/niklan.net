<?php declare(strict_types = 1);

namespace Drupal\external_content\Bundler;

use Drupal\external_content\Contract\Bundler\BundlerFacadeInterface;
use Drupal\external_content\Contract\Bundler\BundlerInterface;
use Drupal\external_content\Contract\Bundler\BundlerResultIdentifiedInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\ContentCollection;
use Drupal\external_content\Data\ExternalContentBundle;
use Drupal\external_content\Data\ExternalContentBundleCollection;
use Drupal\external_content\Data\ExternalContentBundleDocument;
use Drupal\external_content\Node\Content;

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
  public function bundle(ContentCollection $document_collection): ExternalContentBundleCollection {
    $identified_bundles = [];
    $documents = $document_collection->getIterator()->getArrayCopy();

    foreach ($this->environment->getBundlers() as $bundler) {
      \assert($bundler instanceof BundlerInterface);
      // Let each bundler go over whole documents before passing it to the
      // other.
      $this->identifyBundles($bundler, $documents, $identified_bundles);
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
  protected function identifyBundles(BundlerInterface $instance, array $documents, array &$identified_bundles): void {
    foreach ($documents as $key => $document) {
      \assert($document instanceof Content);
      $result = $instance->bundle($document);

      if ($result->isUnidentified()) {
        continue;
      }

      \assert($result instanceof BundlerResultIdentifiedInterface);
      unset($documents[$key]);

      $identified_bundles[$result->id()][] = [
        'document' => $document,
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
      $bundle = new ExternalContentBundle((string) $id);

      foreach ($bundle_documents as $bundle_document) {
        $bundle_document_instance = new ExternalContentBundleDocument(
          $bundle_document['document'],
          $bundle_document['attributes'],
        );
        $bundle->add($bundle_document_instance);
      }

      $bundle_collection->add($bundle);
    }

    return $bundle_collection;
  }

}
