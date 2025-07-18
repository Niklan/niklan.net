<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Unit\Utility;

use Drupal\niklan\Search\Data\EntitySearchResult;
use Drupal\niklan\Search\Utils\SearchApiResultItemsHelper;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Query\ResultSetInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides a test for Search API result items helper.
 *
 * @coversDefaultClass \Drupal\niklan\Search\Utils\SearchApiResultItemsHelper
 */
final class SearchApiResultItemsHelperTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that extracting entity IDs from results are working as expected.
   */
  public function testExtractEntityIds(): void {
    $item_ids = ['entity:node/1:ru', 'entity:node/2:ru', 'entity:user/5:en'];

    $result_items = [];

    foreach ($item_ids as $item_id) {
      $result_item = $this->prophesize(ItemInterface::class);
      $result_item->getId()->willReturn($item_id);
      $result_items[$item_id] = $result_item->reveal();
    }

    $result_set = $this->prophesize(ResultSetInterface::class);
    $result_set->getResultItems()->willReturn($result_items);

    $extracted_ids = SearchApiResultItemsHelper::extractEntityIds($result_set->reveal());
    $expected_ids = [
      'entity:node/1:ru' => new EntitySearchResult('node', '1', 'ru'),
      'entity:node/2:ru' => new EntitySearchResult('node', '2', 'ru'),
      'entity:user/5:en' => new EntitySearchResult('user', '5', 'en'),
    ];
    self::assertEquals($expected_ids, $extracted_ids);
  }

  public function testExtractEntityIdsWithEmptyResultSet(): void {
    $result_set = $this->prophesize(ResultSetInterface::class);
    $result_set->getResultItems()->willReturn([]);

    $extracted_ids = SearchApiResultItemsHelper::extractEntityIds($result_set->reveal());
    self::assertEquals([], $extracted_ids);
  }

  public function testExtractEntityIdsWithDifferentEntityTypes(): void {
    $item_ids = [
      'entity:node/10:ru',
      'entity:user/20:en',
      'entity:comment/30:es',
    ];

    $result_items = [];

    foreach ($item_ids as $item_id) {
      $result_item = $this->prophesize(ItemInterface::class);
      $result_item->getId()->willReturn($item_id);
      $result_items[$item_id] = $result_item->reveal();
    }

    $result_set = $this->prophesize(ResultSetInterface::class);
    $result_set->getResultItems()->willReturn($result_items);

    $extracted_ids = SearchApiResultItemsHelper::extractEntityIds($result_set->reveal());
    $expected_ids = [
      'entity:node/10:ru' => new EntitySearchResult('node', '10', 'ru'),
      'entity:user/20:en' => new EntitySearchResult('user', '20', 'en'),
      'entity:comment/30:es' => new EntitySearchResult('comment', '30', 'es'),
    ];
    self::assertEquals($expected_ids, $extracted_ids);
  }

  public function testExtractEntityIdsWithDifferentLanguages(): void {
    $item_ids = [
      'entity:node/100:de',
      'entity:user/200:fr',
      'entity:comment/300:ja',
    ];

    $result_items = [];

    foreach ($item_ids as $item_id) {
      $result_item = $this->prophesize(ItemInterface::class);
      $result_item->getId()->willReturn($item_id);
      $result_items[$item_id] = $result_item->reveal();
    }

    $result_set = $this->prophesize(ResultSetInterface::class);
    $result_set->getResultItems()->willReturn($result_items);

    $extracted_ids = SearchApiResultItemsHelper::extractEntityIds($result_set->reveal());
    $expected_ids = [
      'entity:node/100:de' => new EntitySearchResult('node', '100', 'de'),
      'entity:user/200:fr' => new EntitySearchResult('user', '200', 'fr'),
      'entity:comment/300:ja' => new EntitySearchResult('comment', '300', 'ja'),
    ];
    self::assertEquals($expected_ids, $extracted_ids);
  }

  public function testExtractEntityIdsWithInvalidFormat(): void {
    $item_id = 'invalid_format';

    $result_item = $this->prophesize(ItemInterface::class);
    $result_item->getId()->willReturn($item_id);

    $result_set = $this->prophesize(ResultSetInterface::class);
    $result_set->getResultItems()->willReturn([$item_id => $result_item->reveal()]);

    $extracted_ids = SearchApiResultItemsHelper::extractEntityIds($result_set->reveal());
    self::assertEquals([], $extracted_ids);
  }

}
