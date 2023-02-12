<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Configuration;

use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Drupal\Component\Plugin\Factory\FactoryInterface;
use Drupal\Component\Plugin\Factory\ReflectionFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;

/**
 * Provides a default plugin manager for external content.
 *
 * External content configuration holds information about specific external
 * content synchronization.
 *
 * @see \Drupal\external_content\Plugin\ExternalContent\Configuration\Configuration
 */
final class ConfigurationPluginManager extends DefaultPluginManager {

  /**
   * The app root.
   */
  protected string $root;

  /**
   * {@inheritdoc}
   */
  protected $defaults = [
    'class' => Configuration::class,
  ];

  /**
   * Constructs a new ExternalContentPluginManager object.
   *
   * @param string $root
   *   The app root.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend.
   */
  public function __construct(string $root, ModuleHandlerInterface $module_handler, CacheBackendInterface $cache) {
    $this->root = $root;
    $this->moduleHandler = $module_handler;
    $this->pluginInterface = ConfigurationInterface::class;

    $this->alterInfo('external_content_configuration_plugins');
    $this->setCacheBackend($cache, 'external_content_configuration_plugins', [
      'external_content_configuration_plugins',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getDiscovery(): DiscoveryInterface {
    if (!$this->discovery) {
      $directories = \array_map(
        fn (Extension $extension) => $this->root . '/' . $extension->getPath(),
        $this->moduleHandler->getModuleList(),
      );
      $this->discovery = new YamlDiscovery('external_content', $directories);
    }

    return $this->discovery;
  }

  /**
   * {@inheritdoc}
   */
  protected function getFactory(): FactoryInterface {
    if (!$this->factory) {
      $this->factory = new ReflectionFactory($this, $this->pluginInterface);
    }

    return $this->factory;
  }

}
