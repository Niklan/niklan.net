<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Plugin\ExternalContent\Environment;

/**
 * Provides a test for environment plugin manager.
 *
 * @covers \Drupal\external_content\Plugin\ExternalContent\Environment\EnvironmentPluginManager
 */
final class EnvironmentPluginManagerTest extends EnvironmentPluginTestBase {

  /**
   * {@selfdoc}
   */
  public function testManager(): void {
    $definitions = $this->environmentPluginManager->getDefinitions();

    self::assertArrayHasKey('foo', $definitions);
    self::assertArrayHasKey('field_item', $definitions);
  }

}
