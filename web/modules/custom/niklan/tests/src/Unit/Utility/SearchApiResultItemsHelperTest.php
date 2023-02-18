<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Unit\Utility;

use Drupal\niklan\Utility\SearchApiResultItemsHelper;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Query\ResultSetInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides a test for Search API result items helper.
 *
 * @coversDefaultClass \Drupal\niklan\Utility\SearchApiResultItemsHelper
 */
final class SearchApiResultItemsHelperTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that extracting entity IDs from results are working as expected.
   */
  public function testExtractEntityIds(): void {
    $item_ids = ['entity:node/1:ru', 'entity:node/2:ru', 'entity:node/5:ru'];

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
      'entity:node/1:ru' => '1',
      'entity:node/2:ru' => '2',
      'entity:node/5:ru' => '5',
    ];
    self::assertEquals($expected_ids, $extracted_ids);
  }

}
