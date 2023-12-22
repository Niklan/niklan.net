<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Plugin\ExternalContent\Environment;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;

/**
 * Defines an external content environment plugin interface.
 *
 * The environment plugin is a proxy for Environment classes and Drupal.
 */
interface EnvironmentPluginInterface extends CacheableDependencyInterface, PluginInspectionInterface {

  /**
   * {@selfdoc}
   */
  public function getEnvironment(): EnvironmentInterface;

  /**
   * {@selfdoc}
   */
  public function label(): string;

}
