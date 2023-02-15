<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Plugin\ExternalContent\Markup;

use Drupal\external_content\Plugin\ExternalContent\Markup\MarkupInterface;
use Drupal\external_content\Plugin\ExternalContent\Markup\MarkupPluginManager;
use Drupal\external_content\Plugin\ExternalContent\Markup\MarkupPluginManagerInterface;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides test for external content markup plugin manager.
 *
 * @coversDefaultClass \Drupal\external_content\Plugin\ExternalContent\Markup\MarkupPluginManager
 */
final class MarkupPluginManagerTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'external_content_test',
  ];

  /**
   * The markup plugin manager.
   */
  protected ?MarkupPluginManager $pluginManager;

  /**
   * Tests that plugin manager works as expected.
   */
  public function testPluginManager(): void {
    $plugin_ids = \array_keys($this->pluginManager->getDefinitions());
    $this->assertContains('external_content_test_foo_bar', $plugin_ids);

    $plugin = $this->pluginManager->createInstance(
      'external_content_test_foo_bar',
    );
    \assert($plugin instanceof MarkupInterface);
    $result = $plugin->convert('<p>foo</p>');
    $this->assertEquals('<p>bar</p>', $result);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->pluginManager = $this->container->get(
      MarkupPluginManagerInterface::class,
    );
  }

}
