<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Traits;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Query\QueryInterface;
use Drupal\search_api\Query\ResultSetInterface;
use Drupal\search_api\Utility\QueryHelperInterface;
use Prophecy\Argument;

/**
 * Provides a trait for search testing.
 *
 * @todo Replace it with a dedicated class.
 */
trait SearchTrait {

  /**
   * Builds a prophecy for query builder.
   *
   * @param array $query_results
   *   The expected search results.
   */
  protected function prepareSearchApiQueryHelper(array $query_results = []): QueryHelperInterface {
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

  /**
   * Builds a prophecy with entity type manager.
   */
  protected function prepareEntityTypeManager(): EntityTypeManagerInterface {
    $index = $this->prophesize(IndexInterface::class);

    $storage = $this->prophesize(EntityStorageInterface::class);
    $storage->load(Argument::any())->willReturn($index->reveal());

    $entity_type_manager = $this->prophesize(EntityTypeManagerInterface::class);
    $entity_type_manager
      ->getStorage('search_api_index')
      ->willReturn($storage->reveal());

    return $entity_type_manager->reveal();
  }

}
