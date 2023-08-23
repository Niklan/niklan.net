<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Kernel\Builder;

use Drupal\external_content\Contract\Builder\ExternalContentRenderArrayBuilderInterface;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\external_content\Node\HtmlElement;
use Drupal\external_content\Node\PlainText;
use Drupal\external_content_test\Builder\HtmlBuilder;
use Drupal\external_content_test\Builder\PlainTextBuilder;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for external content render array builder.
 *
 * @group external_content
 * @covers \Drupal\external_content\Builder\ExternalContentRenderArrayBuilder
 */
final class ExternalContentRenderArrayBuilderTest extends ExternalContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'external_content_test',
  ];

  /**
   * The render array builder.
   */
  protected ExternalContentRenderArrayBuilderInterface $renderArrayBuilder;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->renderArrayBuilder = $this
      ->container
      ->get(ExternalContentRenderArrayBuilderInterface::class);
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

    $external_file = new ExternalContentFile('foo', 'foo/bar.html');
    $external_content_document = new ExternalContentDocument($external_file);
    $external_content_document->addChild($paragraph);

    $environment = new Environment(new Configuration());
    $environment->addBuilder(HtmlBuilder::class);
    $environment->addBuilder(PlainTextBuilder::class);

    $this->renderArrayBuilder->setEnvironment($environment);
    $result = $this->renderArrayBuilder->build($external_content_document);

    $expected_result = [
      '#theme' => 'html_tag',
      '#tag' => 'p',
      'children' => [
        0 => [
          '#markup' => 'Hello, ',
        ],
        1 => [
          '#theme' => 'html_tag',
          '#tag' => 'strong',
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
