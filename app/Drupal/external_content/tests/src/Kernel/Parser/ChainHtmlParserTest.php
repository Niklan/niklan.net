<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Parser;

use Drupal\external_content\Dto\HtmlParserState;
use Drupal\external_content\Dto\PlainTextElement;
use Drupal\external_content\Dto\SourceFile;
use Drupal\external_content\Dto\SourceFileParams;
use Drupal\external_content\Parser\ChainHtmlParser;
use Drupal\external_content\Parser\ChainHtmlParserInterface;
use Drupal\external_content\Plugin\ExternalContent\HtmlParser\HtmlParserPluginManagerInterface;
use Drupal\external_content_test\Dto\FooBarElement;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for default chained HTML parser.
 *
 * @coversDefaultClass \Drupal\external_content\Parser\ChainHtmlParser
 */
final class ChainHtmlParserTest extends ExternalContentTestBase {

  use ProphecyTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  /**
   * The chained HTML parser.
   */
  protected ?ChainHtmlParserInterface $chainHtmlParser;

  /**
   * Tests that parser works as expected.
   */
  public function testParse(): void {
    $html = <<<'HTML'
    <foo-bar>
      Foo
      <foo-bar>Bar</foo-bar>
    </foo-bar>
    HTML;

    $source_file = new SourceFile('/home', '/home/foo.txt');
    $source_file_params = new SourceFileParams([]);
    $parser_state = new HtmlParserState(
      $source_file,
      $source_file_params,
      $this->chainHtmlParser,
    );
    $content = $this->chainHtmlParser->parseRoot($html, $parser_state);

    self::assertEquals(1, $content->getChildren()->count());
    $first = $content->getChildren()->offsetGet(0);
    self::assertInstanceOf(FooBarElement::class, $first);
    self::assertTrue($first->hasChildren());
    $children = $first->getChildren();
    self::assertInstanceOf(PlainTextElement::class, $children->offsetGet(0));
    self::assertStringContainsString(
      'Foo',
      $children->offsetGet(0)->getContent(),
    );
    self::assertInstanceOf(FooBarElement::class, $children->offsetGet(1));
    $children_first_child = $children
      ->offsetGet(1)
      ->getChildren()
      ->offsetGet(0);
    self::assertInstanceOf(PlainTextElement::class, $children_first_child);
    self::assertStringContainsString(
      'Bar',
      $children_first_child->getContent(),
    );
  }

  /**
   * Tests that parser works properly if no parsers provided.
   */
  public function testWithoutParsers(): void {
    $plugin_manager = $this
      ->prophesize(HtmlParserPluginManagerInterface::class);
    $plugin_manager->getDefinitions()->willReturn([]);

    $chain_html_parser = new ChainHtmlParser($plugin_manager->reveal());
    $parser_state = new HtmlParserState(
      new SourceFile('', ''),
      new SourceFileParams([]),
      $chain_html_parser,
    );

    $result = $chain_html_parser->parseRoot('<b>Hello</b>', $parser_state);
    self::assertEquals(0, $result->getChildren()->count());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->chainHtmlParser = $this->container->get(
      ChainHtmlParserInterface::class,
    );
  }

}
