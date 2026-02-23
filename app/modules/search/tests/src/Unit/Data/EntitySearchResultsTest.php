<?php

declare(strict_types=1);

namespace Drupal\Tests\app_search\Unit\Data;

use Drupal\app_search\Data\EntitySearchResult;
use Drupal\app_search\Data\EntitySearchResults;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(EntitySearchResults::class)]
final class EntitySearchResultsTest extends UnitTestCase {

  #[DataProvider('dataProvider')]
  public function testObject(array $input, array $expected): void {
    $results = new EntitySearchResults(
      $input['items'],
      $input['total_results_count'],
    );

    self::assertEquals($input['items'], $results->getItems());
    self::assertEquals(
      $input['items'],
      $results->getIterator()->getArrayCopy(),
    );
    self::assertEquals($expected['count'], $results->count());
    self::assertEquals(
      $input['total_results_count'],
      $results->getTotalResultsCount(),
    );
    self::assertEquals(
      $expected['entity_type_ids'],
      $results->getEntityTypeIds(),
    );
    self::assertEquals($expected['entity_ids'], $results->getEntityIds());
  }

  public static function dataProvider(): \Generator {
    yield 'empty' => [
      'input' => [
        'items' => [],
        'total_results_count' => NULL,
      ],
      'expected' => [
        'count' => 0,
        'entity_type_ids' => [],
        'entity_ids' => [],
      ],
    ];

    yield 'single result' => [
      'input' => [
        'items' => [new EntitySearchResult('node', '1', 'ru')],
        'total_results_count' => 1,
      ],
      'expected' => [
        'count' => 1,
        'entity_type_ids' => ['node'],
        'entity_ids' => [
          'node' => ['1'],
        ],
      ],
    ];

    yield 'two results but 10 total' => [
      'input' => [
        'items' => [
          new EntitySearchResult('node', '1', 'ru'),
          new EntitySearchResult('node', '2', 'ru'),
        ],
        'total_results_count' => 10,
      ],
      'expected' => [
        'count' => 2,
        'entity_type_ids' => ['node'],
        'entity_ids' => [
          'node' => ['1', '2'],
        ],
      ],
    ];

    yield 'different entities' => [
      'input' => [
        'items' => [
          new EntitySearchResult('node', '1', 'ru'),
          new EntitySearchResult('user', '1', 'ru'),
        ],
        'total_results_count' => 2,
      ],
      'expected' => [
        'count' => 2,
        'entity_type_ids' => ['node', 'user'],
        'entity_ids' => [
          'node' => ['1'],
          'user' => ['1'],
        ],
      ],
    ];
  }

}
