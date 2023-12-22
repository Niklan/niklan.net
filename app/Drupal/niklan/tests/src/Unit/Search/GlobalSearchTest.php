<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Unit\Search;

use Drupal\niklan\Data\SearchParams;
use Drupal\niklan\Search\GlobalSearch;
use Drupal\Tests\niklan\Traits\SearchTrait;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for global search.
 *
 * @covers \Drupal\niklan\Search\GlobalSearch
 */
final class GlobalSearchTest extends UnitTestCase {

  use SearchTrait;

  /**
   * Tests that search works as expected.
   *
   * @dataProvider dataProvider
   */
  public function testSearch(SearchParams $params, array $query_results): void {
    $search = new GlobalSearch(
      $this->prepareSearchApiQueryHelper($query_results),
      $this->prepareEntityTypeManager(),
    );

    $search_results = $search->search($params);

    self::assertCount(\count($query_results), $search_results);
  }

  /**
   * Provides data for testing.
   */
  public function dataProvider(): \Generator {
    yield [new SearchParams(NULL, 10), []];
    yield [
      new SearchParams('Drupal', 10),
      ['entity:node/1:ru', 'entity:node/2:ru'],
    ];
  }

}
