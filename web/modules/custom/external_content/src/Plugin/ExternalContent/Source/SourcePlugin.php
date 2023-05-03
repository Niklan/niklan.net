<?php declare(strict_types = 1);

namespace Drupal\extern_content\Plugin\ExternalContent\Source;

use Drupal\Core\Plugin\PluginBase;
use Drupal\external_content\Contract\SourcePluginInterface;

/**
 * Provides a basic source plugin implementation.
 */
abstract class SourcePlugin extends PluginBase implements SourcePluginInterface {

  /**
   * {@inheritdoc}
   */
  public function grouperPluginId(): string {
    return self::DEFAULT_GROUPER_PLUGIN_ID;
  }

}
