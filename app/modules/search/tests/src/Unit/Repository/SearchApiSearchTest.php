<?php

declare(strict_types=1);

namespace Drupal\Tests\app_search\Unit\Repository;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\app_search\Repository\SearchApiSearch;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Query\QueryInterface;
use Drupal\search_api\Query\ResultSetInterface;
use Drupal\search_api\Utility\QueryHelperInterface;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Prophecy\Argument;

#[CoversClass(SearchApiSearch::class)]
final class SearchApiSearchTest extends UnitTestCase {

  /**
   * Tests that abstract class works in base implementation.
   */
  public function testObject(): void {
    $search = $this->buildImplementation(['entity:node/1:ru']);

    self::assertEmpty($search->getCacheContexts());
    self::assertEquals(['search_api_list:test'], $search->getCacheTags());
    self::assertEquals(Cache::PERMANENT, $search->getCacheMaxAge());

    // Make sure that query is properly build and executed.
    $results = $search->getResults();
    self::assertEquals('entity:node/1:ru', $results[0]->getId());
  }

  /**
   * Builds a simple implementation.
   *
   * @param array $query_results
   *   The expected query results.
   */
  protected function buildImplementation(array $query_results): SearchApiSearch {
    $query_helper = $this->prepareSearchApiQueryHelper($query_results);
    $entity_type_manger = $this->prepareEntityTypeManager();

    return new class($query_helper, $entity_type_manger) extends SearchApiSearch {

      public function getResults(): array {
        return parent::getQuery()->execute()->getResultItems();
      }

      #[\Override]
      protected function getIndexId(): string {
        return 'test';
      }

    };
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
