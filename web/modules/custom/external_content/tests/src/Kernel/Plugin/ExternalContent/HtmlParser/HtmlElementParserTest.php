<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Plugin\ExternalContent\HtmlParser;

use Drupal\external_content\Dto\HtmlElement;
use Drupal\external_content\Dto\HtmlParserState;
use Drupal\external_content\Dto\PlainTextElement;
use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileParams;
use Drupal\external_content\Parser\ChainHtmlParserInterface;
use Drupal\external_content\Plugin\ExternalContent\HtmlParser\HtmlParserPluginManagerInterface;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides a test for HTML parser plugins.
 *
 * @coversDefaultClass \Drupal\external_content\Plugin\ExternalContent\HtmlParser\HtmlElementParser
 */
final class HtmlElementParserTest extends ExternalContentTestBase {

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
    $plugin = $this->pluginManager->createInstance('html_element');

    $html = '<div class="foo-bar" data-test="bar-baz"><p>Hello, world!</p></div>';

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
    self::assertInstanceOf(HtmlElement::class, $result);
    self::assertEquals('div', $result->getTag());
    self::assertCount(2, $result->getAttributes());
    self::assertEquals(
      ['class' => 'foo-bar', 'data-test' => 'bar-baz'],
      $result->getAttributes(),
    );
    self::assertTrue($result->hasAttribute('class'));
    self::assertEquals('foo-bar', $result->getAttribute('class'));
    self::assertTrue($result->hasAttribute('data-test'));
    self::assertEquals('bar-baz', $result->getAttribute('data-test'));

    $p_element = $result->getChildren()->offsetGet(0);
    self::assertInstanceOf(HtmlElement::class, $p_element);
    self::assertEquals('p', $p_element->getTag());
    $text_element = $p_element->getChildren()->offsetGet(0);
    self::assertEmpty($p_element->getAttributes());
    self::assertInstanceOf(PlainTextElement::class, $text_element);
    self::assertEquals('Hello, world!', $text_element->getContent());
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
