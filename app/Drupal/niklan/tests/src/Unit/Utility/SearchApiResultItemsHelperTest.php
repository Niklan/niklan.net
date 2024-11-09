<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Unit\Utility;

use Drupal\niklan\Helper\SearchApiResultItemsHelper;
use Drupal\niklan\Search\Data\EntitySearchResult;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Query\ResultSetInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides a test for Search API result items helper.
 *
 * @coversDefaultClass \Drupal\niklan\Helper\SearchApiResultItemsHelper
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

}
