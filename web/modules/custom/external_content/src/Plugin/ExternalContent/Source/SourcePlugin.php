<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Source;

use Drupal\Core\Plugin\PluginBase;
use Drupal\external_content\Contract\SourcePluginInterface;
use Drupal\external_content\Data\SourceConfiguration;

/**
 * Provides a basic source plugin implementation.
 */
abstract class SourcePlugin extends PluginBase implements SourcePluginInterface {

  /**
   * {@inheritdoc}
   */
  public function toConfiguration(): SourceConfiguration {
    return new SourceConfiguration(
      $this->workingDir(),
      $this->grouperPluginId(),
      $this->getPluginId(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function grouperPluginId(): string {
    return self::DEFAULT_GROUPER_PLUGIN_ID;
  }

  /**
   * {@inheritdoc}
   */
  public function isActive(): bool {
    return TRUE;
  }

}
