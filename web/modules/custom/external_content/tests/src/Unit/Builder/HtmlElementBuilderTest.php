<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Builder;

use Drupal\external_content\Builder\Html\ElementRenderArrayBuilder;
use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Node\Html\Element;
use Drupal\external_content\Node\Node;
use Drupal\Tests\UnitTestCaseTest;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Builder\Html\ElementRenderArrayBuilder
 * @group external_content
 */
final class HtmlElementBuilderTest extends UnitTestCaseTest {

  /**
   * {@selfdoc}
   */
  public function testValidElement(): void {
    $element = new Element('div', new Attributes(['foo' => 'bar']));
    $builder = new ElementRenderArrayBuilder();
    $result = $builder->build($element, [], []);
    $expected_result = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'foo' => 'bar',
      ],
      'children' => [],
    ];

    self::assertTrue($result->isBuilt());
    self::assertEquals($expected_result, $result->getRenderArray());
  }

  /**
   * {@selfdoc}
   */
  public function testInvalidElement(): void {
    $element = new class() extends Node {};
    $builder = new ElementRenderArrayBuilder();
    $result = $builder->build($element, [], []);

    self::assertTrue($result->isNotBuild());
  }

}
