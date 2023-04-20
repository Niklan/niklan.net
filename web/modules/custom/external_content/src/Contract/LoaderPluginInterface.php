<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContentCollection;

/**
 * Represents an interface for loader plugins.
 */
interface LoaderPluginInterface {

  /**
   * Loads external content to the website.
   *
   * @param \Drupal\external_content\Data\ExternalContentCollection $external_content_collection
   *   The collection with external content.
   */
  public function load(ExternalContentCollection $external_content_collection): void;

}
