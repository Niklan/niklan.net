<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Loader;

use Drupal\external_content\Dto\ExternalContentCollection;

/**
 * Represents an interface for loader plugins.
 */
interface LoaderInterface {

  /**
   * Loads external content to the website.
   *
   * @param \Drupal\external_content\Dto\ExternalContentCollection $external_content_collection
   *   The collection with external content.
   */
  public function load(ExternalContentCollection $external_content_collection): void;

}
