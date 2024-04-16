<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Builder\Html;

use Drupal\external_content\Builder\Html\ElementRenderArrayRenderArrayBuilder;
use Drupal\external_content\Builder\RenderArrayBuilder;
use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Node\Element;
use Drupal\external_content\Node\Node;
use Drupal\Tests\UnitTestCaseTest;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Builder\Html\ElementRenderArrayRenderArrayBuilder
 * @group external_content
 */
final class ElementRenderArrayBuilderTest extends UnitTestCaseTest {

  /**
   * {@selfdoc}
   */
  public function testValidElement(): void {
    $element = new Element('div', new Attributes(['foo' => 'bar']));
    $builder = new ElementRenderArrayRenderArrayBuilder();
    $result = $builder->build($element, RenderArrayBuilder::class);
    $expected_result = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'foo' => 'bar',
      ],
      'children' => [],
    ];

    self::assertTrue($result->isBuilt());
    self::assertEquals($expected_result, $result->result());
  }

  /**
   * {@selfdoc}
   */
  public function testInvalidElement(): void {
    $element = new class () extends Node {};
    $builder = new ElementRenderArrayRenderArrayBuilder();
    self::assertFalse(
      $builder->supportsBuild($element, RenderArrayBuilder::class),
    );
  }

}
