<?php

declare(strict_types=1);

namespace Drupal\Tests\app_search\Unit\Repository;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\app_search\Data\SearchParams;
use Drupal\app_search\Repository\GlobalSearch;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Query\QueryInterface;
use Drupal\search_api\Query\ResultSetInterface;
use Drupal\search_api\Utility\QueryHelperInterface;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Prophecy\Argument;

#[CoversClass(GlobalSearch::class)]
final class GlobalSearchTest extends UnitTestCase {

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

  private function prepareSearchApiQueryHelper(array $query_results = []): QueryHelperInterface {
    $result_items = [];

    foreach ($query_results as $search_result) {
      $result_item = $this->prophesize(ItemInterface::class);
      $result_item->getId()->willReturn($search_result);
      $result_items[] = $result_item->reveal();
    }

    $result_set = $this->prophesize(ResultSetInterface::class);
    $result_set->getResultItems()->willReturn($result_items);
    $result_set->getResultCount()->willReturn(\count($query_results));

    $query = $this->prophesize(QueryInterface::class);
    $query->keys(Argument::cetera())->willReturn(NULL);
    $query->range(Argument::cetera())->willReturn(NULL);
    $query->sort(Argument::cetera())->willReturn(NULL);
    $query->execute()->willReturn($result_set->reveal());

    $query_helper = $this->prophesize(QueryHelperInterface::class);
    $query_helper->createQuery(Argument::any())->willReturn($query->reveal());

    return $query_helper->reveal();
  }

  private function prepareEntityTypeManager(): EntityTypeManagerInterface {
    $index = $this->prophesize(IndexInterface::class);

    $storage = $this->prophesize(EntityStorageInterface::class);
    $storage->load(Argument::any())->willReturn($index->reveal());

    $entity_type_manager = $this->prophesize(EntityTypeManagerInterface::class);
    $entity_type_manager->getStorage('search_api_index')->willReturn($storage->reveal());

    return $entity_type_manager->reveal();
  }

}
