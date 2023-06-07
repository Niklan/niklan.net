<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContent;
use Drupal\external_content\Data\ExternalContentCollection;

/**
 * Represents an interface for loader plugins.
 */
interface LoaderPluginInterface {

  /**
   * Loads a collection external content to the website.
   *
   * @param \Drupal\external_content\Data\ExternalContentCollection $external_content_collection
   *   The collection with external content.
   */
  public function loadMultiple(ExternalContentCollection $external_content_collection): void;

  /**
   * Loads a single external content to the website.
   *
   * @param \Drupal\external_content\Data\ExternalContent $external_content
   *   The external content.
   */
  public function load(ExternalContent $external_content): void;

}
