<?php

declare(strict_types=1);

namespace Drupal\Tests\app_search\Unit\Data;

use Drupal\app_search\Data\EntitySearchResult;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(EntitySearchResult::class)]
final class EntitySearchResultTest extends UnitTestCase {

  #[DataProvider('dataProvider')]
  public function testObject(string $entity_type_id, int|string $entity_id, string $language): void {
    $item = new EntitySearchResult($entity_type_id, $entity_id, $language);

    self::assertEquals($entity_type_id, $item->entityTypeId);
    self::assertEquals($entity_id, $item->entityId);
    self::assertEquals($language, $item->language);
  }

  public static function dataProvider(): \Generator {
    yield ['node', '1', 'ru'];
    yield ['node', 1, 'en'];
  }

}
