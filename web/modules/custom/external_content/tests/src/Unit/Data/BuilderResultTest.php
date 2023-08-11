<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Data;

use Drupal\external_content\Data\BuilderResult;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a tests for builder result instances.
 *
 * @covers \Drupal\external_content\Data\BuilderResult
 * @group external_content
 */
final class BuilderResultTest extends UnitTestCase {

  /**
   * Tests none builder result.
   *
   * @covers \Drupal\external_content\Data\BuilderResultNone
   */
  public function testNone(): void {
    $result = BuilderResult::none();

    self::assertFalse($result->isBuilt());
    self::assertTrue($result->isNotBuild());
  }

  /**
   * Tests render array builder result.
   *
   * @covers \Drupal\external_content\Data\BuilderResultRenderArray
   */
  public function testRenderArray(): void {
    $render_array = ['#markup' => 'Hello, World!'];
    $result = BuilderResult::renderArray($render_array);

    self::assertTrue($result->isBuilt());
    self::assertFalse($result->isNotBuild());
    self::assertEquals($render_array, $result->getRenderArray());
  }

}
