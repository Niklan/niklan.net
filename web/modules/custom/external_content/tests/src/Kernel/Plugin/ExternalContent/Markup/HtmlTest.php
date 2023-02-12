<?php

declare(strict_types = 1);

namespace Drupal\Tests\external_content\Plugin\ExternalContent\Markup;

use Drupal\external_content\Plugin\ExternalContent\Markup\MarkupPluginManager;
use Drupal\external_content\Plugin\ExternalContent\Markup\MarkupPluginManagerInterface;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides test for HTML markup plugin.
 *
 * @coversDefaultClass \Drupal\external_content\Plugin\ExternalContent\Markup\Html
 */
final class HtmlTest extends ExternalContentTestBase {

  /**
   * The markup plugin manager.
   */
  protected ?MarkupPluginManager $pluginManager;

  /**
   * Tests that plugin works as expected.
   */
  public function testPlugin(): void {
    $content = '<p>Hello, world!</p>';

    /** @var \Drupal\external_content\Plugin\ExternalContent\Markup\MarkupInterface $plugin */
    $plugin = $this->pluginManager->createInstance('html');
    $result = $plugin->convert($content);
    $this->assertEquals($content, $result);
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
