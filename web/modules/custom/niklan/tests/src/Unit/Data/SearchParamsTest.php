<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Unit\Data;

use Drupal\niklan\Data\SearchParams;
use Drupal\Tests\UnitTestCase;

/**
 * Provides a test for search params.
 *
 * @coversDefaultClass \Drupal\niklan\Data\SearchParams
 */
final class SearchParamsTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   *
   * @dataProvider dataProvider
   */
  public function testObject(?string $keys, int $limit, int $offset = 0): void {
    $params = new SearchParams($keys, $limit, $offset);

    self::assertEquals($params->getKeys(), $keys);
    self::assertEquals($params->getLimit(), $limit);
    self::assertEquals($params->getOffset(), $offset);
  }

  /**
   * Provides data for testing.
   */
  public function dataProvider(): \Generator {
    yield 'without keys' => [NULL, 10];
    yield 'with keys' => ['foo', 20];
    yield 'with offset' => [NULL, 10, 50];
  }

}
