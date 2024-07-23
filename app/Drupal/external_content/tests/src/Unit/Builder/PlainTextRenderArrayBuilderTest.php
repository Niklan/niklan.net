<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Builder;

use Drupal\external_content\Builder\PlainTextRenderArrayBuilder;
use Drupal\external_content\Contract\Builder\ChildRenderArrayBuilderInterface;
use Drupal\external_content\Node\PlainText;
use Drupal\Tests\UnitTestCaseTest;

/**
 * @covers \Drupal\external_content\Builder\PlainTextRenderArrayBuilder
 * @group external_content
 */
final class PlainTextRenderArrayBuilderTest extends UnitTestCaseTest {

  public function testValidElement(): void {
    $child_builder = $this->prophesize(ChildRenderArrayBuilderInterface::class);
    $child_builder = $child_builder->reveal();

    $element = new PlainText('Hello, World!');
    $builder = new PlainTextRenderArrayBuilder();
    $result = $builder->build($element, $child_builder);
    $expected_result = [
      '#markup' => 'Hello, World!',
    ];

    self::assertTrue($result->isBuilt());
    self::assertEquals($expected_result, $result->result());
  }

}
