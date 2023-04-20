<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Parser;

use Drupal\external_content\Builder\ChainRenderArrayBuilder;
use Drupal\external_content\Contract\BuilderPluginManagerInterface;
use Drupal\external_content\Contract\ChainRenderArrayBuilderInterface;
use Drupal\external_content\Data\HtmlElement;
use Drupal\external_content\Data\PlainTextElement;
use Drupal\external_content\Plugin\ExternalContent\Builder\HtmlElementBuilder;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for default chained render array builder..
 *
 * @coversDefaultClass \Drupal\external_content\Builder\ChainRenderArrayBuilder
 */
final class ChainRenderArrayBuilderTest extends ExternalContentTestBase {

  use ProphecyTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  /**
   * The chain render array builder.
   */
  protected ?ChainRenderArrayBuilderInterface $chainBuilder;

  /**
   * Tests that chain builder works properly.
   *
   * It assumes that there at least two default builders are presented: html,
   * plain_text.
   */
  public function testBuilder(): void {
    // These structure is basically:
    // @code
    // <p data-foo="bar">
    //   Hello, World! <a href="https://example.com">This is a link</a> inside a
    //   paragraph.
    // </p>
    // @endcode
    $paragraph = new HtmlElement('p', ['data-foo' => 'bar']);
    $paragraph->addChild(new PlainTextElement('Hello, World! '));
    $link = new HtmlElement('a', ['href' => 'https://example.com']);
    $link->addChild(new PlainTextElement('This is a link'));
    $paragraph->addChild($link);
    $paragraph->addChild(new PlainTextElement(' inside a paragraph.'));

    $build = $this->chainBuilder->build($paragraph);
    $expected = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#attributes' => [
        'data-foo' => 'bar',
      ],
      0 => [
        '#markup' => 'Hello, World! ',
      ],
      1 => [
        '#type' => 'html_tag',
        '#tag' => 'a',
        '#attributes' => [
          'href' => 'https://example.com',
        ],
        0 => [
          '#markup' => 'This is a link',
        ],
      ],
      2 => [
        '#markup' => ' inside a paragraph.',
      ],
    ];

    self::assertEquals($expected, $build);
  }

  /**
   * Tests that builder properly handles no builders provided.
   */
  public function testBuildWithoutBuilders(): void {
    $builder_manager = $this->prophesize(BuilderPluginManagerInterface::class);
    $builder_manager->getDefinitions()->willReturn([]);

    $paragraph = new HtmlElement('p', ['data-foo' => 'bar']);
    $paragraph->addChild(new PlainTextElement('Hello, World! '));

    $chain_builder = new ChainRenderArrayBuilder($builder_manager->reveal());
    $result = $chain_builder->build($paragraph);

    self::assertEquals([], $result);
  }

  /**
   * Tests that builders instantiated only once.
   */
  public function testBuildersInitOnce(): void {
    $definition_calls = 0;

    $builder_manager = $this->prophesize(BuilderPluginManagerInterface::class);
    $builder_manager
      ->getDefinitions()
      ->will(static function () use (&$definition_calls): array {
        $definition_calls++;

        return [
          'foo' => [
            'id' => 'test',
          ],
        ];
      });
    $builder_manager
      ->createInstance(Argument::exact('foo'))
      ->willReturn(new HtmlElementBuilder());

    $paragraph = new HtmlElement('p', ['data-foo' => 'bar']);
    $paragraph->addChild(new PlainTextElement('Hello, World! '));

    $chain_builder = new ChainRenderArrayBuilder($builder_manager->reveal());

    self::assertEquals(0, $definition_calls);
    $chain_builder->build($paragraph);
    self::assertEquals(1, $definition_calls);
    // After initial $builders are set, it should not build them again. All
    // consecutive calls should return 1.
    $chain_builder->build($paragraph);
    self::assertEquals(1, $definition_calls);

  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->chainBuilder = $this
      ->container
      ->get(ChainRenderArrayBuilder::class);
  }

}
