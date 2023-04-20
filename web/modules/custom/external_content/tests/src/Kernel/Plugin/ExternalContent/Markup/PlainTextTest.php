<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Plugin\ExternalContent\Markup;

use Drupal\external_content\Contract\MarkupPluginInterface;
use Drupal\external_content\Plugin\ExternalContent\Markup\MarkupPluginManager;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides test for plain text markup plugin.
 *
 * @coversDefaultClass \Drupal\external_content\Plugin\ExternalContent\Markup\PlainText
 */
final class PlainTextTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['filter'];

  /**
   * The markup plugin manager.
   */
  protected ?MarkupPluginManager $pluginManager;

  /**
   * Tests that plugin works as expected.
   */
  public function testPlugin(): void {
    $content = <<<'TEXT'
    Hello, world!
    Today is a good day.

    Foo, bar, baz?
    TEXT;

    $expected = <<<'HTML'
    <p>Hello, world!<br />
    Today is a good day.</p>
    <p>Foo, bar, baz?</p>

    HTML;

    $plugin = $this->pluginManager->createInstance('plain_text');
    \assert($plugin instanceof MarkupPluginInterface);
    $result = $plugin->convert($content);
    $this->assertEquals($expected, $result);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->pluginManager = $this->container->get(MarkupPluginManager::class);
  }

}
