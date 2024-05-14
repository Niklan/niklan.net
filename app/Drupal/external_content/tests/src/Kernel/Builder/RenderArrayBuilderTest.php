<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Builder;

use Drupal\external_content\Builder\ElementRenderArrayBuilder;
use Drupal\external_content\Builder\PlainTextRenderArrayBuilder;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Node\Element;
use Drupal\external_content\Node\PlainText;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for external content render array builder.
 *
 * @group external_content
 * @covers \Drupal\external_content\Builder\RenderArrayBuilder
 */
final class RenderArrayBuilderTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  /**
   * {@selfdoc}
   */
  public function testBuild(): void {
    $paragraph = new Element('p');
    $paragraph->addChild(new PlainText('Hello, '));
    $paragraph->addChild(
      (new Element('strong'))->addChild(new PlainText('World')),
    );
    $paragraph->addChild(new PlainText('!'));

    $external_content_document = new Content();
    $external_content_document->addChild($paragraph);

    $environment = new Environment();
    $environment->addRenderArrayBuilder(new NoneRenderArrayBuilder());
    $environment->addRenderArrayBuilder(new ElementRenderArrayBuilder());
    $environment->addRenderArrayBuilder(new PlainTextRenderArrayBuilder());

    self::assertTrue(
      $this
        ->getRenderArrayBuilder()
        ->supportsBuild($paragraph, RenderArrayBuilder::class),
    );

    $this->getRenderArrayBuilder()->setEnvironment($environment);
    $result = $this->getRenderArrayBuilder()->build($external_content_document);

    $expected_result = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#attributes' => [],
      'children' => [
        0 => [
          '#markup' => 'Hello, ',
        ],
        1 => [
          '#type' => 'html_tag',
          '#tag' => 'strong',
          '#attributes' => [],
          'children' => [
            0 => [
              '#markup' => 'World',
            ],
          ],
        ],
        2 => [
          '#markup' => '!',
        ],
      ],
    ];

    self::assertEquals($expected_result, $result->result());
  }

  /**
   * {@selfdoc}
   */
  private function getRenderArrayBuilder(): RenderArrayBuilder {
    return $this->container->get(RenderArrayBuilder::class);
  }

}
