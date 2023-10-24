<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Builder;

use Drupal\external_content\Builder\HtmlElementBuilder;
use Drupal\external_content\Builder\PlainTextBuilder;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderFacadeInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Node\HtmlElement;
use Drupal\external_content\Node\PlainText;
use Drupal\external_content\Source\File;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for external content render array builder.
 *
 * @group external_content
 * @covers \Drupal\external_content\Builder\RenderArrayBuilderFacade
 */
final class RenderArrayBuilderFacadeTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  /**
   * The render array builder.
   */
  protected RenderArrayBuilderFacadeInterface $renderArrayBuilder;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->renderArrayBuilder = $this
      ->container
      ->get(RenderArrayBuilderFacadeInterface::class);
  }

  /**
   * {@selfdoc}
   */
  public function testBuild(): void {
    $paragraph = new HtmlElement('p');
    $paragraph->addChild(new PlainText('Hello, '));
    $paragraph->addChild(
      (new HtmlElement('strong'))->addChild(new PlainText('World')),
    );
    $paragraph->addChild(new PlainText('!'));

    $external_file = new File('foo', 'foo/bar.html');
    $external_content_document = new Content($external_file);
    $external_content_document->addChild($paragraph);

    $environment = new Environment();
    $environment->addBuilder(new HtmlElementBuilder());
    $environment->addBuilder(new PlainTextBuilder());

    $this->renderArrayBuilder->setEnvironment($environment);
    $result = $this->renderArrayBuilder->build($external_content_document);

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

    self::assertEquals($expected_result, $result->getRenderArray());
  }

}
