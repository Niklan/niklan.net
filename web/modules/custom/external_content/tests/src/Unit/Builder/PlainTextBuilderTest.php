<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Builder;

use Drupal\external_content\Builder\PlainTextBuilder;
use Drupal\external_content\Node\Node;
use Drupal\external_content\Node\PlainText;
use Drupal\Tests\UnitTestCaseTest;

/**
 * {@selfdoc}
 *
 * @covers \Drupal\external_content\Builder\PlainTextBuilder
 * @group external_content
 */
final class PlainTextBuilderTest extends UnitTestCaseTest {

  /**
   * {@selfdoc}
   */
  public function testValidElement(): void {
    $element = new PlainText('Hello, World!');
    $builder = new PlainTextBuilder();
    $result = $builder->build($element, []);
    $expected_result = [
      '#markup' => 'Hello, World!',
    ];

    self::assertTrue($result->isBuilt());
    self::assertEquals($expected_result, $result->getRenderArray());
  }

  /**
   * {@selfdoc}
   */
  public function testInvalidElement(): void {
    $element = new class() extends Node {};
    $builder = new PlainTextBuilder();
    $result = $builder->build($element, []);

    self::assertTrue($result->isNotBuild());
  }

}
