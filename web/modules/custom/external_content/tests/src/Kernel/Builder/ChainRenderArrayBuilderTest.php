<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Kernel\Parser;

use Drupal\external_content\Builder\ChainRenderArrayBuilder;
use Drupal\external_content\Builder\ChainRenderArrayBuilderInterface;
use Drupal\external_content\Dto\HtmlElement;
use Drupal\external_content\Dto\PlainTextElement;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides test for default chained render array builder..
 *
 * @coversDefaultClass \Drupal\external_content\Builder\ChainRenderArrayBuilder
 */
final class ChainRenderArrayBuilderTest extends ExternalContentTestBase {

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
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->chainBuilder = $this
      ->container
      ->get(ChainRenderArrayBuilder::class);
  }

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

}
