<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Kernel\Plugin\ExternalContent\HtmlParser;

use Drupal\external_content\Dto\HtmlParserState;
use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileParams;
use Drupal\external_content\Parser\ChainHtmlParserInterface;
use Drupal\external_content\Plugin\ExternalContent\HtmlParser\HtmlParserPluginManagerInterface;
use Drupal\external_content_test\Dto\FooBarElement;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PropertyTrait;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides a test for HTML parser plugins.
 */
final class HtmlParserPluginTest extends ExternalContentTestBase {

  use PropertyTrait;

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
   * The chain HTML parser.
   */
  protected ?ChainHtmlParserInterface $chainHtmlParser;

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
    $source_file = new SourceFile('/home', '/home/foo.txt');
    $source_file_params = new SourceFileParams([]);
    $parser_state = new HtmlParserState(
      $source_file,
      $source_file_params,
      $this->chainHtmlParser,
    );
    /** @var \Drupal\external_content_test\Dto\FooBarElement $result */
    $result = $plugin->parse($node, $parser_state);
    self::assertInstanceOf(FooBarElement::class, $result);
    self::assertStringContainsString(
      'Content inside the element.',
      $result->getChildren()->offsetGet(0)->getContent(),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->pluginManager = $this->container->get(
      HtmlParserPluginManagerInterface::class,
    );
    $this->chainHtmlParser = $this->container->get(
      ChainHtmlParserInterface::class,
    );
  }

}
