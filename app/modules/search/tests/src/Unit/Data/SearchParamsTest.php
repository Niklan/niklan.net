<?php

declare(strict_types=1);

namespace Drupal\Tests\app_search\Unit\Data;

use Drupal\app_search\Data\SearchParams;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(SearchParams::class)]
final class SearchParamsTest extends UnitTestCase {

  #[DataProvider('dataProvider')]
  public function testObject(?string $keys, int $limit, int $offset = 0): void {
    $params = new SearchParams($keys, $limit, $offset);

    self::assertEquals($params->getKeys(), $keys);
    self::assertEquals($params->getLimit(), $limit);
    self::assertEquals($params->getOffset(), $offset);
  }

  public static function dataProvider(): \Generator {
    yield 'without keys' => [NULL, 10];
    yield 'with keys' => ['foo', 20];
    yield 'with offset' => [NULL, 10, 50];
  }

}
