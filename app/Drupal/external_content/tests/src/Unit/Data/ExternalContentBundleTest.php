<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\ContentBundle;
use Drupal\external_content\Data\IdentifierSource;
use Drupal\external_content\Node\Content;
use Drupal\Tests\UnitTestCase;

/**
 * Provides an external content bundle test.
 *
 * @covers \Drupal\external_content\Data\ContentBundle
 * @group external_content
 */
final class ExternalContentBundleTest extends UnitTestCase {

  /**
   * {@selfdoc}
   */
  public function testGetByAttribute(): void {
    $bundle = new ContentBundle('hooks');

    self::assertEquals('hooks', $bundle->id);

    $doc_en = new IdentifierSource(
      new Content(),
      (new Attributes())->setAttribute('language', 'en'),
    );

    $doc_ru = new IdentifierSource(
      new Content(),
      (new Attributes())->setAttribute('language', 'ru'),
    );

    $doc_d10 = new IdentifierSource(
      new Content(),
      (new Attributes())->setAttribute('drupal', '10'),
    );

    $bundle->add($doc_en);
    $bundle->add($doc_ru);
    $bundle->add($doc_d10);

    self::assertCount(3, $bundle);
    self::assertEquals(
      [$doc_en, $doc_ru, $doc_d10],
      $bundle->getIterator()->getArrayCopy(),
    );

    $result = $bundle->getByAttribute('language');
    self::assertCount(2, $result);

    $not_existing_data = $bundle->getByAttributeValue('language', 'il');
    self::assertCount(0, $not_existing_data);
  }

  /**
   * {@selfdoc}
   */
  public function testGetByAttributeValue(): void {
    $bundle = new ContentBundle('hooks');
    $bundle->add(new IdentifierSource(
      new Content(),
      (new Attributes())->setAttribute('language', 'en'),
    ));
    $bundle->add(new IdentifierSource(
      new Content(),
      (new Attributes())->setAttribute('language', 'ru'),
    ));
    $bundle->add(new IdentifierSource(
      new Content(),
      (new Attributes())->setAttribute('drupal', '10'),
    ));

    self::assertCount(3, $bundle);

    $result = $bundle->getByAttributeValue('drupal', '10');
    self::assertCount(1, $result);
  }

}
