<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Plugin\ExternalContent\Configuration;

use Drupal\external_content\Plugin\ExternalContent\Configuration\ConfigurationInterface;
use Drupal\external_content\Plugin\ExternalContent\Configuration\ConfigurationPluginManager;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides test for external content plugin manager.
 */
final class ConfigurationPluginManagerTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'external_content_test',
  ];

  /**
   * The external content plugin manager.
   */
  protected ?ConfigurationPluginManager $pluginManager;

  /**
   * Tests that plugin manager works as expected.
   */
  public function testPluginManager(): void {
    $plugin_ids = \array_keys($this->pluginManager->getDefinitions());
    $this->assertContains('test', $plugin_ids);

    $instance = $this->pluginManager->createInstance('test');
    $this->assertInstanceOf(ConfigurationInterface::class, $instance);
    $this->assertEquals('test', $instance->id());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->pluginManager = $this->container->get('plugin.manager.external_content.configuration');
  }

}
