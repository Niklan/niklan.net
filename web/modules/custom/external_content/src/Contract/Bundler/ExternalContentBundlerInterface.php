<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Bundler;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Data\ExternalContentBundleCollection;
use Drupal\external_content\Data\ExternalContentDocumentCollection;

/**
 * Represents an external content bundler.
 */
interface ExternalContentBundlerInterface extends EnvironmentAwareInterface {

  /**
   * Bundles external documents.
   *
   * @param \Drupal\external_content\Data\ExternalContentDocumentCollection $document_collection
   *   The collections of documents.
   */
  public function bundle(ExternalContentDocumentCollection $document_collection): ExternalContentBundleCollection;

}
