<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Unit\Search;

use Drupal\niklan\Search\Data\SearchParams;
use Drupal\niklan\Search\Repository\GlobalSearch;
use Drupal\Tests\niklan\Traits\SearchTrait;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(GlobalSearch::class)]
final class GlobalSearchTest extends UnitTestCase {

  use SearchTrait;

  #[DataProvider('dataProvider')]
  public function testSearch(SearchParams $params, array $query_results): void {
    $search = new GlobalSearch(
      $this->prepareSearchApiQueryHelper($query_results),
      $this->prepareEntityTypeManager(),
    );

    $search_results = $search->search($params);

    self::assertCount(\count($query_results), $search_results);
  }

  public static function dataProvider(): \Generator {
    yield [new SearchParams(NULL, 10), []];
    yield [
      new SearchParams('Drupal', 10),
      ['entity:node/1:ru', 'entity:node/2:ru'],
    ];
  }

}
