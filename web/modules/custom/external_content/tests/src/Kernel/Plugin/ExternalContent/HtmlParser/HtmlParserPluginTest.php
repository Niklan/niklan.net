<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Kernel\Plugin\ExternalContent\HtmlParser;

use Drupal\external_content\Plugin\ExternalContent\HtmlParser\HtmlParserPluginManagerInterface;
use Drupal\external_content_test\Dto\TestElement;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides a test for HTML parser plugins.
 */
final class HtmlParserPluginTest extends ExternalContentTestBase {

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
   * Tests that simple plugin for a single non nested element working properly.
   */
  public function testSimplePlugin(): void {
    /** @var \Drupal\external_content\Plugin\ExternalContent\HtmlParser\HtmlParserInterface $plugin */
    $plugin = $this->pluginManager->createInstance('foo_bar');

    $html = '<foo-bar>Content inside the element.</foo-bar>';
    $crawler = new Crawler($html);
    $node = $crawler->filter('body')->getNode(0)->firstChild;

    self::assertTrue($plugin::isApplicable($node));
    /** @var \Drupal\external_content_test\Dto\TestElement $result */
    $result = $plugin->parse($node);
    self::assertInstanceOf(TestElement::class, $result);
    self::assertEquals($node->nodeValue, $result->getContent());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->pluginManager = $this->container->get(HtmlParserPluginManagerInterface::class);
  }

}
