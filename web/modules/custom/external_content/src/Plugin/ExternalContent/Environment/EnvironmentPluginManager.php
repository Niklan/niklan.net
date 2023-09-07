<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Environment;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginManagerInterface;

/**
 * Provides an external content environment plugin manager.
 */
final class EnvironmentPluginManager extends DefaultPluginManager implements EnvironmentPluginManagerInterface {

  /**
   * {@selfdoc}
   */
  protected const PLUGIN_SUBDIR = 'Plugin/ExternalContent/Environment';

  /**
   * {@selfdoc}
   */
  protected const PLUGIN_INTERFACE = 'Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginInterface';

  /**
   * {@selfdoc}
   */
  protected const PLUGIN_ANNOTATION_NAME = 'Drupal\external_content\Annotation\ExternalContentEnvironment';

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      self::PLUGIN_SUBDIR,
      $namespaces,
      $module_handler,
      self::PLUGIN_INTERFACE,
      self::PLUGIN_ANNOTATION_NAME,
    );

    $this->alterInfo('external_content_environment_plugin_info');
    $this->setCacheBackend(
      $cache_backend,
      'external_content_environment_plugin_definition',
    );
  }

}
