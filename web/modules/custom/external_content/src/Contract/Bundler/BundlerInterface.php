<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Bundler;

use Drupal\external_content\Node\ExternalContentDocument;

/**
 * Represents an external content bundler.
 */
interface BundlerInterface {

  /**
   * Compares two documents and decide, should they be matched or not.
   *
   * @param \Drupal\external_content\Node\ExternalContentDocument $document_a
   *   The document to compare.
   * @param \Drupal\external_content\Node\ExternalContentDocument $document_b
   *   The document to compare.
   */
  public function compare(ExternalContentDocument $document_a, ExternalContentDocument $document_b): BundlerCompareResultInterface;

}
