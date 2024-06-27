<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Data;

use Drupal\external_content\Data\RenderArrayBuilderResult;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a tests for builder result instances.
 *
 * @covers \Drupal\external_content\Data\RenderArrayBuilderResult
 * @group external_content
 */
final class RenderArrayBuilderResultTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testEmpty(): void {
    $result = RenderArrayBuilderResult::empty();

    self::assertFalse($result->isBuilt());
    self::assertTrue($result->isNotBuild());
    self::assertEmpty($result->result());
  }

  /**
   * {@selfdoc}
   */
  public function testWithRenderArray(): void {
    $render_array = ['#markup' => 'Hello, World!'];
    $result = RenderArrayBuilderResult::withRenderArray($render_array);

    self::assertTrue($result->isBuilt());
    self::assertFalse($result->isNotBuild());
    self::assertEquals($render_array, $result->result());
  }

}
