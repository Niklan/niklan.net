<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Builder\Html;

use Drupal\external_content\Builder\Html\PlainTextRenderArrayRenderArrayBuilder;
use Drupal\external_content\Builder\RenderArrayBuilder;
use Drupal\external_content\Node\Node;
use Drupal\external_content\Node\PlainText;
use Drupal\Tests\UnitTestCaseTest;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Builder\Html\PlainTextRenderArrayRenderArrayBuilder
 * @group external_content
 */
final class PlainTextRenderArrayBuilderTest extends UnitTestCaseTest {

  /**
   * {@selfdoc}
   */
  public function testValidElement(): void {
    $element = new PlainText('Hello, World!');
    $builder = new PlainTextRenderArrayRenderArrayBuilder();
    $result = $builder->build($element, RenderArrayBuilder::class);
    $expected_result = [
      '#markup' => 'Hello, World!',
    ];

    self::assertTrue($result->isBuilt());
    self::assertEquals($expected_result, $result->result());
  }

  /**
   * {@selfdoc}
   */
  public function testInvalidElement(): void {
    $element = new class () extends Node {};
    $builder = new PlainTextRenderArrayRenderArrayBuilder();

    self::assertFalse(
      $builder->supportsBuild($element, RenderArrayBuilder::class),
    );
  }

}
