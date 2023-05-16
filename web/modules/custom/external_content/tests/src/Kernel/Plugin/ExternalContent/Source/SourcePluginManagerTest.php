<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Plugin\ExternalContent\Source;

use Drupal\external_content\Contract\SourcePluginInterface;
use Drupal\external_content\Contract\SourcePluginManagerInterface;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for source plugin manager.
 *
 * @coversDefaultClass \Drupal\external_content\Plugin\ExternalContent\Source\SourcePluginManager
 */
final class SourcePluginManagerTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  /**
   * The source plugin manager.
   */
  protected SourcePluginManagerInterface $pluginManager;

  /**
   * Tests that plugin manager creates instance properly.
   */
  public function testCreateAnInstance(): void {
    $instance = $this->pluginManager->createInstance('foo');

    self::assertInstanceOf(SourcePluginInterface::class, $instance);
    self::assertEquals('foo', $instance->getPluginId());
    self::assertEquals('public://foo', $instance->workingDir());
    self::assertEquals('false', $instance->grouperPluginId());

    $configuration = $instance->toConfiguration();

    self::assertEquals('public://foo', $configuration->getWorkingDir());
    self::assertEquals('false', $configuration->getGroupingPluginId());
    self::assertEquals('foo', $configuration->getSourcePluginId());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->pluginManager = $this->container->get(
      SourcePluginManagerInterface::class,
    );
  }

}
