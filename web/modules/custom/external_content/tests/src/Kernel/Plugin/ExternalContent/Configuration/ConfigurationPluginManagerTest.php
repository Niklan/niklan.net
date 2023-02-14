<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Plugin\ExternalContent\Configuration;

use Drupal\external_content\Plugin\ExternalContent\Configuration\ConfigurationInterface;
use Drupal\external_content\Plugin\ExternalContent\Configuration\ConfigurationPluginManager;
use Drupal\migrate\Plugin\Exception\BadPluginDefinitionException;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides test for external content plugin manager.
 *
 * @coversDefaultClass \Drupal\external_content\Plugin\ExternalContent\Configuration\ConfigurationPluginManager
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
    self::assertContains('test', $plugin_ids);

    $instance = $this->pluginManager->createInstance('test');
    self::assertInstanceOf(ConfigurationInterface::class, $instance);
    self::assertEquals('test', $instance->id());
    self::assertEquals('public://external-content', $instance->workingDir());
    self::assertEquals('params', $instance->grouperPluginId());
  }

  /**
   * Tests that missing 'working_dir' property throws exception.
   */
  public function testWorkingDirIsNotDefined(): void {
    self::expectException(BadPluginDefinitionException::class);
    $this->pluginManager->createInstance('working_dir_is_not_defined');
  }

  /**
   * Tests that grouper plugin can be changed.
   */
  public function testGrouperOverride(): void {
    $instance = $this->pluginManager->createInstance('grouper_plugin');
    self::assertEquals('foo_bar', $instance->grouperPluginId());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->pluginManager = $this->container->get(
      ConfigurationPluginManager::class,
    );
  }

}
