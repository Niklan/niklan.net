<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Plugin\ExternalContent\HtmlParser;

use Drupal\external_content\Plugin\ExternalContent\HtmlParser\HtmlParserPluginManagerInterface;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides test for external content HTML parser plugin manager.
 *
 * @coversDefaultClass \Drupal\external_content\Plugin\ExternalContent\HtmlParser\HtmlParserPluginManager
 */
final class HtmlParserPluginManagerTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'external_content_test',
  ];

  /**
   * The HTML parser plugin manager.
   */
  protected ?HtmlParserPluginManagerInterface $pluginManager;

  /**
   * Tests that plugin manager works as expected.
   */
  public function testPluginManager(): void {
    $plugin_ids = \array_keys($this->pluginManager->getDefinitions());
    $this->assertContains('foo_bar', $plugin_ids);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->pluginManager = $this->container->get(
      HtmlParserPluginManagerInterface::class,
    );
  }

}
