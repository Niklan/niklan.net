<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\LoaderResult;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a loader result DTO test.
 *
 * @covers \Drupal\external_content\Data\LoaderResult
 * @group external_content
 */
final class LoaderResultTest extends UnitTestCase {

  /**
   * Tests the entity result.
   *
   * @covers \Drupal\external_content\Data\LoaderResultEntity
   */
  public function testEntityResult(): void {
    $result = LoaderResult::entity('node', '1');

    self::assertTrue($result->isSuccess());
    self::assertFalse($result->isNotSuccess());
    self::assertFalse($result->shouldContinue());
    self::assertEquals('node', $result->getEntityTypeId());
    self::assertEquals('1', $result->getEntityId());
  }

  /**
   * Tests the 'ignore' result.
   *
   * @covers \Drupal\external_content\Data\LoaderResultIgnore
   */
  public function testIgnoreResult(): void {
    $result = LoaderResult::ignore();

    self::assertFalse($result->isSuccess());
    self::assertTrue($result->isNotSuccess());
    self::assertFalse($result->shouldContinue());
  }

  /**
   * Tests the 'skip' result.
   *
   * @covers \Drupal\external_content\Data\LoaderResultSkip
   */
  public function testSkipResult(): void {
    $result = LoaderResult::skip();

    self::assertFalse($result->isSuccess());
    self::assertTrue($result->isNotSuccess());
    self::assertTrue($result->shouldContinue());
  }

}
