<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Plugin\ExternalContent\Environment;

use Drupal\Core\Cache\Cache;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginInterface;

/**
 * Provides a test for environment plugin.
 *
 * @covers \Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentPlugin
 */
final class EnvironmentPluginTest extends EnvironmentPluginTestBase {

  /**
   * {@selfdoc}
   */
  public function testManager(): void {
    $plugin = $this->environmentPluginManager->createInstance('foo');

    self::assertInstanceOf(EnvironmentPluginInterface::class, $plugin);
    self::assertEquals('foo', $plugin->getPluginId());
    self::assertEquals('Foo environment', $plugin->label());
    self::assertEquals([], $plugin->getCacheContexts());
    self::assertEquals([], $plugin->getCacheTags());
    self::assertEquals(Cache::PERMANENT, $plugin->getCacheMaxAge());

    $environment = $plugin->getEnvironment();
    $configuration = $environment->getConfiguration();

    self::assertEquals('Oh, hello there!', $configuration->get(self::class));
  }

}
