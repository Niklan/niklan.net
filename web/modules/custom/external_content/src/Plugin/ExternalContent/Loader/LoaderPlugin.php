<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Loader;

use Drupal\Core\Plugin\PluginBase;
use Drupal\external_content\Contract\LoaderPluginInterface;
use Drupal\external_content\Data\ExternalContentCollection;

/**
 * Provides a basic implementation for a loader plugin.
 */
abstract class LoaderPlugin extends PluginBase implements LoaderPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function loadMultiple(ExternalContentCollection $external_content_collection): void {
    foreach ($external_content_collection as $external_content) {
      $this->load($external_content);
    }
  }

}
