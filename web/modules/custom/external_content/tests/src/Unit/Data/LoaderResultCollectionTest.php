<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\LoaderResult;
use Drupal\external_content\Data\LoaderResultCollection;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a loader result collection tests.
 *
 * @covers \Drupal\external_content\Data\LoaderResultCollection
 * @group external_content
 */
final class LoaderResultCollectionTest extends UnitTestCase {

  /**
   * Tests the object.
   */
  public function testObject(): void {
    $instance = new LoaderResultCollection();

    self::assertCount(0, $instance);
    self::assertEquals([], $instance->getIterator()->getArrayCopy());

    $result_a = LoaderResult::skip();
    $result_b = LoaderResult::ignore();
    $result_c = LoaderResult::entity('node', '1');

    $instance
      ->addResult($result_a)
      ->addResult($result_b)
      ->addResult($result_c);

    self::assertCount(3, $instance);
    self::assertEquals(
      [$result_a, $result_b, $result_c],
      $instance->getIterator()->getArrayCopy(),
    );

    $success_collection = $instance->getSuccessful();

    self::assertCount(1, $success_collection);
    self::assertEquals(
      [$result_c],
      $success_collection->getIterator()->getArrayCopy(),
    );
  }

}
