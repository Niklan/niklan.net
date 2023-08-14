<?php declare(strict_types = 1);

namespace Drupal\Tests\external_content\Unit\Data;

use Drupal\external_content\Data\Attributes;
use Drupal\external_content\Data\ExternalContentBundle;
use Drupal\external_content\Data\ExternalContentBundleDocument;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\external_content\Node\ExternalContentDocument;
use Drupal\Tests\UnitTestCase;

/**
 * Provides an external content bundle test.
 *
 * @covers \Drupal\external_content\Data\ExternalContentBundle
 * @group external_content
 */
final class ExternalContentBundleTest extends UnitTestCase {

  /**
   * Tests getting documents from bundle by attribute.
   */
  public function testGetByAttribute(): void {
    $bundle = new ExternalContentBundle('hooks');

    self::assertEquals('hooks', $bundle->getId());

    $doc_en = new ExternalContentBundleDocument(
      new ExternalContentDocument(new ExternalContentFile('foo', 'bar')),
      (new Attributes())->setAttribute('language', 'en'),
    );

    $doc_ru = new ExternalContentBundleDocument(
      new ExternalContentDocument(new ExternalContentFile('foo', 'bar')),
      (new Attributes())->setAttribute('language', 'ru'),
    );

    $doc_d10 = new ExternalContentBundleDocument(
      new ExternalContentDocument(new ExternalContentFile('foo', 'bar')),
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
   * Tests getting documents from bundle by attribute and value.
   */
  public function testGetByAttributeValue(): void {
    $bundle = new ExternalContentBundle('hooks');
    $bundle->add(new ExternalContentBundleDocument(
      new ExternalContentDocument(new ExternalContentFile('foo', 'bar')),
      (new Attributes())->setAttribute('language', 'en'),
    ));
    $bundle->add(new ExternalContentBundleDocument(
      new ExternalContentDocument(new ExternalContentFile('foo', 'bar')),
      (new Attributes())->setAttribute('language', 'ru'),
    ));
    $bundle->add(new ExternalContentBundleDocument(
      new ExternalContentDocument(new ExternalContentFile('foo', 'bar')),
      (new Attributes())->setAttribute('drupal', '10'),
    ));

    self::assertCount(3, $bundle);

    $result = $bundle->getByAttributeValue('drupal', '10');
    self::assertCount(1, $result);
  }

}
