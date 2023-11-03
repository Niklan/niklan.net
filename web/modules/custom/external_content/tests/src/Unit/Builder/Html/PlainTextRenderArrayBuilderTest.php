<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Builder\Html;

use Drupal\external_content\Builder\Html\PlainTextRenderArrayBuilder;
use Drupal\external_content\Builder\RenderArrayBuilder;
use Drupal\external_content\Node\Html\PlainText;
use Drupal\external_content\Node\Node;
use Drupal\Tests\UnitTestCaseTest;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Builder\Html\PlainTextRenderArrayBuilder
 * @group external_content
 */
final class PlainTextRenderArrayBuilderTest extends UnitTestCaseTest {

  /**
   * {@selfdoc}
   */
  public function testValidElement(): void {
    $element = new PlainText('Hello, World!');
    $builder = new PlainTextRenderArrayBuilder();
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
    $element = new class() extends Node {};
    $builder = new PlainTextRenderArrayBuilder();

    self::assertFalse(
      $builder->supportsBuild($element, RenderArrayBuilder::class),
    );
  }

}
