<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Unit\Search;

use Drupal\Core\Cache\Cache;
use Drupal\niklan\Search\SearchApiSearch;
use Drupal\Tests\niklan\Traits\SearchTrait;
use Drupal\Tests\UnitTestCase;

/**
 * Provides test for an abstract Search API search.
 *
 * @coversDefaultClass \Drupal\niklan\Search\SearchApiSearch
 */
final class SearchApiSearchTest extends UnitTestCase {

  use SearchTrait;

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

      /**
       * {@inheritdoc}
       */
      protected function getIndexId(): string {
        return 'test';
      }

      /**
       * Gets results from query.
       *
       * @return array
       *   An array with query results.
       */
      public function getResults(): array {
        return parent::getQuery()->execute()->getResultItems();
      }

    };
  }

}
