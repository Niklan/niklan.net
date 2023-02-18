<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Unit\Data;

use Drupal\niklan\Data\ContentEntityResultSet;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a test for result set DTO.
 *
 * @coversDefaultClass \Drupal\niklan\Data\ContentEntityResultSet
 */
final class ContentEntityResultSetTest extends UnitTestCase {

  /**
   * Tests object works as expected.
   */
  public function testObject(): void {
    $result_set = new ContentEntityResultSet('node', ['1', '2', '3'], 10);

    self::assertEquals('node', $result_set->getEntityTypeId());
    self::assertEquals(['1', '2', '3'], $result_set->getIds());
    self::assertEquals(10, $result_set->getResultCount());
    self::assertEquals(3, $result_set->count());
    self::assertEquals('3', $result_set->getIterator()->offsetGet(2));
  }

}
