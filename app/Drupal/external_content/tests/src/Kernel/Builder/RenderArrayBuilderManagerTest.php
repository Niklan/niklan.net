<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Kernel\Builder;

use Drupal\external_content\Builder\ContentRenderArrayBuilder;
use Drupal\external_content\Builder\ElementRenderArrayBuilder;
use Drupal\external_content\Builder\PlainTextRenderArrayBuilder;
use Drupal\external_content\Contract\Builder\RenderArrayBuilderManagerInterface;
use Drupal\external_content\Environment\Environment;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Node\Element;
use Drupal\external_content\Node\PlainText;
use Drupal\Tests\external_content\Kernel\ExternalContentTestBase;

/**
 * Provides a test for external content render array builder.
 *
 * @group external_content
 * @covers \Drupal\external_content\Builder\RenderArrayBuilderManager
 */
final class RenderArrayBuilderManagerTest extends ExternalContentTestBase {

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

    $environment = new Environment('test');
    $environment->addRenderArrayBuilder(new ContentRenderArrayBuilder());
    $environment->addRenderArrayBuilder(new ElementRenderArrayBuilder());
    $environment->addRenderArrayBuilder(new PlainTextRenderArrayBuilder());

    $result = $this->getManager()->build(
      node: $external_content_document,
      environment: $environment,
    );

    self::assertTrue($result->isBuilt());
    self::assertFalse($result->isNotBuild());

    $build = $result->result();
    self::assertSame($build[0]['#type'], 'html_tag');
    self::assertCount(3, $build[0]['children']);
  }

  /**
   * {@selfdoc}
   */
  private function getManager(): RenderArrayBuilderManagerInterface {
    return $this->container->get(RenderArrayBuilderManagerInterface::class);
  }

}
