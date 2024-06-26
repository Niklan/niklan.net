<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Builder;

use Drupal\Core\Render\Element\HtmlTag;
use Drupal\external_content\Builder\ElementRenderArrayBuilder;
use Drupal\external_content\Contract\Builder\ChildRenderArrayBuilderInterface;
use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Node\Element;
use Drupal\external_content\Node\Node;
use Drupal\Tests\UnitTestCaseTest;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Builder\ElementRenderArrayBuilder
 * @group external_content
 * @todo Make it kernel with a proper testing of 'pre_render'.
 */
final class ElementRenderArrayBuilderTest extends UnitTestCaseTest {

  /**
   * {@selfdoc}
   */
  public function testValidElement(): void {
    $child_builder = $this->prophesize(ChildRenderArrayBuilderInterface::class);
    $child_builder = $child_builder->reveal();

    $element = new Element('div', new Attributes(['foo' => 'bar']));
    $builder = new ElementRenderArrayBuilder();
    $result = $builder->build($element, $child_builder);
    $expected_result = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'foo' => 'bar',
      ],
      '#pre_render' => [
        HtmlTag::preRenderHtmlTag(...),
        ElementRenderArrayBuilder::preRenderTag(...),
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
    $builder = new ElementRenderArrayBuilder();
    self::assertFalse($builder->supportsBuild($element));
  }

}
