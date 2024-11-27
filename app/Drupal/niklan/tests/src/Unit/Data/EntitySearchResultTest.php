<?php

declare(strict_types=1);

namespace Drupal\Tests\niklan\Unit\Data;

use Drupal\niklan\Search\Data\EntitySearchResult;
use Drupal\Tests\UnitTestCase;

/**
 * Tests the entity search result item DTO.
 *
 * @coversDefaultClass \Drupal\niklan\Search\Data\EntitySearchResult
 */
final class EntitySearchResultTest extends UnitTestCase {

  /**
   * Tests that object works as expected.
   *
   * @param string $entity_type_id
   *   The entity type ID.
   * @param int|string $entity_id
   *   The entity ID.
   * @param string $language
   *   The entity langcode.
   *
   * @dataProvider dataProvider
   */
  public function testObject(string $entity_type_id, int|string $entity_id, string $language): void {
    $item = new EntitySearchResult($entity_type_id, $entity_id, $language);

    self::assertEquals($entity_type_id, $item->getEntityTypeId());
    self::assertEquals($entity_id, $item->getEntityId());
    self::assertEquals($language, $item->getLanguage());
  }

  /**
   * Provides testing data.
   */
  public function dataProvider(): \Generator {
    yield ['node', '1', 'ru'];
    yield ['node', 1, 'en'];
  }

}
